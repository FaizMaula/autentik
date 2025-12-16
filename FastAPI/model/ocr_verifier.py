import os, cv2, torch
from PIL import Image
from datetime import datetime
from rapidfuzz import fuzz
from googleapiclient.discovery import build
import google.generativeai as genai
import numpy as np
from google.api_core.exceptions import ResourceExhausted
from dotenv import load_dotenv


# Import global model loader
from model.ocr_loader import processor, model, reader, device


def process_certificate(
    nama,
    tahun_akademik,
    penyelenggara,
    tanggal_mulai,
    tanggal_selesai,
    nama_kegiatan,
    nama_kegiatan_inggris,
    berkas,
    image_path
):

    # === 0. Parsing tanggal ===
    def parse_html_date(date_str):
        if not date_str:
            return None
        try:
            return datetime.strptime(date_str, "%Y-%m-%d")
        except ValueError:
            return None

    dt_mulai = parse_html_date(tanggal_mulai)
    dt_selesai = parse_html_date(tanggal_selesai)

    tanggal_normalized = (
        dt_mulai.strftime("%d %B %Y") if dt_mulai else "Unknown"
    )

    # === 1. Variasi format tanggal ===
    def generate_date_variations(dt):
        if not dt:
            return []
        return [
            dt.strftime("%d/%m/%Y"),
            dt.strftime("%d-%m-%Y"),
            dt.strftime("%d %b %Y"),
            dt.strftime("%B %d, %Y"),
            dt.strftime("%Y/%m/%d"),
            dt.strftime("%Y-%m-%d"),
            dt.strftime("%d %B %Y"),
        ]

    date_variations_mulai = generate_date_variations(dt_mulai)
    date_variations_selesai = generate_date_variations(dt_selesai)

    if not os.path.exists(image_path):
        raise ValueError(f"File not found: {image_path}")

    # Aman untuk file besar → tidak OOM saat load
    with open(image_path, "rb") as f:
        file_bytes = np.frombuffer(f.read(), np.uint8)

    img = cv2.imdecode(file_bytes, cv2.IMREAD_COLOR)

    if img is None:
        raise ValueError("Failed to load image. The file may be corrupted or too large.")

    h, w = img.shape[:2]

    MAX_SIZE = 1600

    # Resize aman sebelum EasyOCR
    if max(h, w) > MAX_SIZE:
        scale = MAX_SIZE / max(h, w)
        img = cv2.resize(img, (int(w*scale), int(h*scale)), interpolation=cv2.INTER_AREA)

    # Convert ke grayscale
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # Kurangi noise
    gray = cv2.bilateralFilter(gray, 11, 17, 17)

    # Adaptive threshold
    thresh = cv2.adaptiveThreshold(
        gray, 255,
        cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
        cv2.THRESH_BINARY,
        31, 10
    )

    # Simpan hasil preprocessing
    cleaned_path = "cleaned_certificate.jpg"
    cv2.imwrite(cleaned_path, thresh)

    # === 3. OCR EasyOCR (global reader) ===
    results = reader.readtext(cleaned_path)

    # === 4. OCR TroCR (global model) ===
    # final_texts = []

    # for i, (bbox, text_easy, prob) in enumerate(results):
    #     if prob < 0.01:
    #         continue

    #     x_min = int(min([p[0] for p in bbox]))
    #     y_min = int(min([p[1] for p in bbox]))
    #     x_max = int(max([p[0] for p in bbox]))
    #     y_max = int(max([p[1] for p in bbox]))

    #     crop = img[y_min:y_max, x_min:x_max]
    #     crop_path = f"crop_{i}.jpg"
    #     cv2.imwrite(crop_path, crop)

    #     # TroCR inference
    #     image = Image.open(crop_path).convert("RGB")
    #     pixel_values = processor(
    #         images=image,
    #         return_tensors="pt"
    #     ).pixel_values.to(device)

    #     with torch.no_grad():
    #         generated_ids = model.generate(pixel_values)

    #     text_trocr = processor.batch_decode(
    #         generated_ids,
    #         skip_special_tokens=True
    #     )[0]

    #     acc = fuzz.ratio(text_easy.lower(), text_trocr.lower())
    #     final_texts.append((text_easy, text_trocr, prob, acc))
    
    # === 4. OCR TroCR (FILTERED) ===
    final_texts = []

    CONF_THRESHOLD = 0.6
    KEYWORDS = [
        "nama", "name", "participant", "peserta",
        "certificate", "sertifikat",
        "tanggal", "date",
        "webinar", "workshop", "seminar",
        penyelenggara.lower()
    ]

    MAX_TROCR_BOXES = 25  # proteksi GPU
    trocr_count = 0

    for i, (bbox, text_easy, prob) in enumerate(results):

        if prob < 0.01:
            continue

        text_lower = text_easy.lower()

        # === FILTER 1: confidence rendah ATAU keyword penting ===
        use_trocr = (
            prob < CONF_THRESHOLD or
            any(k in text_lower for k in KEYWORDS)
        )

        if not use_trocr:
            # pakai EasyOCR saja
            final_texts.append((text_easy, text_easy, prob, 100))
            continue

        # === LIMIT JUMLAH TroCR ===
        if trocr_count >= MAX_TROCR_BOXES:
            final_texts.append((text_easy, text_easy, prob, 100))
            continue

        # === Crop bbox ===
        x_min = int(min(p[0] for p in bbox))
        y_min = int(min(p[1] for p in bbox))
        x_max = int(max(p[0] for p in bbox))
        y_max = int(max(p[1] for p in bbox))

        crop = img[y_min:y_max, x_min:x_max]
        if crop.size == 0:
            continue

        image = Image.fromarray(crop).convert("RGB")

        pixel_values = processor(
            images=image,
            return_tensors="pt"
        ).pixel_values.to(device)

        with torch.no_grad():
            generated_ids = model.generate(pixel_values)

        text_trocr = processor.batch_decode(
            generated_ids,
            skip_special_tokens=True
        )[0]

        acc = fuzz.ratio(text_easy.lower(), text_trocr.lower())

        final_texts.append((text_easy, text_trocr, prob, acc))
        trocr_count += 1


    # Gabungkan hasil TroCR yang valid
    final_output = " ".join([
        t for (_, t, p, a) in final_texts if p > 0.01
    ])

    # === 5. Fuzzy match ===
    targets = {
        "nama": nama,
        # "tahun_akademik": tahun_akademik,
        "penyelenggara": penyelenggara,
        # "tanggal_mulai": date_variations_mulai,
        "tanggal_selesai": date_variations_selesai,
        "nama_kegiatan": nama_kegiatan,
        # "nama_kegiatan_inggris": nama_kegiatan_inggris,
        # "berkas": berkas,
    }

    match_scores = {}
    for key, value in targets.items():
        if isinstance(value, list) and value:
            match_scores[key] = max(
                fuzz.partial_ratio(final_output.lower(), v.lower())
                for v in value
            )
        else:
            match_scores[key] = fuzz.partial_ratio(
                final_output.lower(),
                str(value).lower()
            )

    # === 6. Google Search ===
    from googleapiclient.discovery import build

    # API credentials
    from googleapiclient.discovery import build

    API_KEY = os.getenv("API_KEY")
    SEARCH_ENGINE_ID = os.getenv("SEARCH_ENGINE_ID")

    def google_search(nama_kegiatan, penyelenggara, num_results=5):
        service = build("customsearch", "v1", developerKey=API_KEY)

        query = f"{nama_kegiatan} {penyelenggara}"

        res = service.cse().list(
            q=query,
            cx=SEARCH_ENGINE_ID,
            num=num_results,
            lr="lang_id"
        ).execute()

        items = res.get("items", [])
        results = []

        for item in items:
            results.append({
                "title": item.get("title"),
                "link": item.get("link"),
                "description": item.get("snippet", "-")
            })

        return results

    S_search = 0
    google_results = google_search(nama_kegiatan, penyelenggara, num_results=5)
    # print(google_results)

    if not google_results:
        verifikasi_text = "Tidak ada hasil pencarian relevan."
        top_result = None
    else:
        top_result = google_results[0]

        genai.configure(api_key=os.getenv("GEMINI_API_KEY"))
        model_gem = genai.GenerativeModel("gemini-2.5-flash-lite")

        prompt = f"""
        Anda adalah AI Verifikator Dokumen untuk kegiatan akademik.

        Data Kegiatan:
        - Nama Kegiatan (ID): "{nama_kegiatan}"
        - Nama Kegiatan (EN): "{nama_kegiatan_inggris}"
        - Tanggal: "{tanggal_normalized}"
        - Penyelenggara: "{penyelenggara}"

        Hasil Pencarian Google (Top Result):
        - Judul: {top_result['title']}
        - Deskripsi: {top_result['description']}
        - Link: {top_result['link']}

        Tugas Anda:
        1. Tentukan apakah kegiatan ini **sesuai** dengan data di Google.
        2. Jawaban harus **3 baris** persis:
        - Baris 1: YA atau TIDAK (sesuai / tidak sesuai)
        - Baris 2: Alasan singkat (1–2 kalimat)
        - Baris 3: Ringkasan kegiatan yang sesuai atau catatan jika tidak ditemukan

        Contoh output:
        YA
        Judul dan deskripsi cocok dengan nama kegiatan dan penyelenggara.
        Kegiatan sesuai ditemukan: [judul kegiatan]

        TIDAK
        Judul dan deskripsi berbeda dengan kegiatan yang diberikan.
        Tidak ditemukan kegiatan yang sesuai.
        """

        try:
            response = model_gem.generate_content(prompt)
            verifikasi_text = response.text
            first_line = verifikasi_text.strip().splitlines()[0].lower()

            if "ya" in first_line:
                S_search = 30
            else:
                S_search = 0

        except ResourceExhausted:
            verifikasi_text = (
                "⚠️ Verifikasi AI sementara tidak tersedia karena batas kuota tercapai.\n"
                "Silakan coba kembali beberapa saat lagi."
            )




    S_final = (
        (match_scores.get("nama_kegiatan", 0)
        + match_scores.get("nama", 0) + match_scores.get("penyelenggara", 0)
        +match_scores.get("tanggal_selesai", 0)) / 4
    ) * 0.7 + S_search 
    return {
        "match_scores": match_scores,
        "final_score": S_final,
        "verifikasi_ai": verifikasi_text,
        "ocr_text": final_output,
        "google_results": google_results
    } 
