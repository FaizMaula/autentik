import os, cv2, torch
from PIL import Image
from datetime import datetime
from rapidfuzz import fuzz
from googleapiclient.discovery import build
import google.generativeai as genai
import numpy as np
from google.api_core.exceptions import ResourceExhausted
from pdf2image import convert_from_path



# Import global model loader
from model.ocr_loader import processor, model, reader, device
from model.font_loader import predict_font

def load_image_any(path):
    ext = os.path.splitext(path)[1].lower()

    # === PDF ===
    if ext == ".pdf":
        pages = convert_from_path(path, dpi=300)
        if not pages:
            raise ValueError("PDF has no pages")

        # Ambil halaman pertama
        img_pil = pages[0].convert("RGB")
        return np.array(img_pil)

    # === IMAGE ===
    with open(path, "rb") as f:
        file_bytes = np.frombuffer(f.read(), np.uint8)

    img = cv2.imdecode(file_bytes, cv2.IMREAD_COLOR)
    if img is None:
        raise ValueError("Failed to load image")

    return img

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
    # === TEMP FOLDER UNTUK FONT CLASSIFIER ===

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
    img = load_image_any(image_path)

    h, w = img.shape[:2]

    MAX_SIZE = 3200

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
    
    font_results = []
    
    targets = {
        "nama": nama,
        "nama_kegiatan": nama_kegiatan,
        "penyelenggara": penyelenggara,
        "tanggal_selesai": date_variations_selesai
    }
    FUZZ_THRESHOLD = 70


    for i, (bbox, text_easy, prob) in enumerate(results):

        if prob < 0.01:
            continue
        # === Crop bbox (dipakai untuk OCR & Font) ===
        x_min = int(min(p[0] for p in bbox))
        y_min = int(min(p[1] for p in bbox))
        x_max = int(max(p[0] for p in bbox))
        y_max = int(max(p[1] for p in bbox))

        crop = img[y_min:y_max, x_min:x_max]
        if crop.size == 0:
            continue

        image = Image.fromarray(crop).convert("RGB")

        image_np = np.array(image)
        do_font_classification = False
        for key, val in targets.items():
            if not val:
                continue
            if isinstance(val, list):
                match_score = max(fuzz.partial_ratio(text_easy.lower(), str(v).lower()) for v in val)
            else:
                match_score = fuzz.partial_ratio(text_easy.lower(), str(val).lower())

            if match_score >= FUZZ_THRESHOLD:
                do_font_classification = True
                break

        font_pred = predict_font(image_np) if do_font_classification else None

        font_results.append({
            "text": text_easy,
            "font_class": font_pred["class"] if font_pred else None,
            "google_font": font_pred["google_font"][0] if font_pred and font_pred["google_font"] else None,
            "style": font_pred["google_font"][1] if font_pred and font_pred["google_font"] else None,
            "font_confidence": float(font_pred["confidence"]) if font_pred else None,
            "ocr_confidence": float(prob),
            "bbox": {
                "x_min": x_min,
                "y_min": y_min,
                "x_max": x_max,
                "y_max": y_max
            }
        })
        

        text_lower = text_easy.lower()

        # === FILTER 1: confidence rendah ATAU keyword penting ===
        use_trocr = (
            prob < CONF_THRESHOLD or
            any(k in text_lower for k in KEYWORDS)
        )

        if not use_trocr:
            # pakai EasyOCR saja
            final_texts.append({
                "easyocr": text_easy,
                "trocr": text_easy,
                "confidence": prob,
                "accuracy": 100,
                "font": font_pred
            })
            continue

        # === LIMIT JUMLAH TroCR ===
        if trocr_count >= MAX_TROCR_BOXES:
            final_texts.append({
                "easyocr": text_easy,
                "trocr": text_easy,
                "confidence": prob,
                "accuracy": 100,
                "font": font_pred
            })
            continue

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

        final_texts.append({
            "easyocr": text_easy,
            "trocr": text_trocr,
            "confidence": prob,
            "accuracy": acc,
            "font": font_pred
        })

        trocr_count += 1

    # Gabungkan hasil TroCR yang valid
    final_output = " ".join([
        item["trocr"]
        for item in final_texts
        if item["confidence"] > 0.01
    ])


    # === 5. Fuzzy match ===
    # Use English field keys for consistency across languages
    targets = {
        "name": nama,
        # "academic_year": tahun_akademik,
        "organizer": penyelenggara,
        # "start_date": date_variations_mulai,
        "end_date": date_variations_selesai,
        "event_name": nama_kegiatan,
        # "event_name_english": nama_kegiatan_inggris,
        # "file": berkas,
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

    API_KEY = os.getenv("API_KEY")
    SEARCH_ENGINE_ID = os.getenv("SEARCH_ENGINE_ID")
    
    from googleapiclient.errors import HttpError
    import time

    # Simple in-memory cache
    CACHE = {}

    def google_search(nama_kegiatan, penyelenggara, num_results=5):
        query = f"{nama_kegiatan} {penyelenggara}"

        # Check cache dulu
        if query in CACHE:
            return CACHE[query]

        service = build("customsearch", "v1", developerKey=API_KEY)

        try:
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

            # Simpan ke cache
            CACHE[query] = results
            return results

        except HttpError as e:
            if e.resp.status == 429:
                print("⚠️ Quota Google Custom Search habis. Tidak bisa melakukan request hari ini.")
            else:
                print(f"⚠️ Terjadi HttpError: {e}")
            return []  # return kosong supaya aplikasi tidak crash


    S_search = 0
    google_results = google_search(nama_kegiatan, penyelenggara, num_results=5)
    # print(google_results)

    if not google_results:
        verifikasi_text = "Tidak ada hasil pencarian relevan."
        top_result = None
    else:
        top_result = google_results[0]

        genai.configure(api_key=os.getenv("GEMINI_API_KEY"))
        model_gem = genai.GenerativeModel("gemini-2.5-flash")

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
        YA Sesuai
        Judul dan deskripsi cocok dengan nama kegiatan dan penyelenggara.
        Kegiatan sesuai ditemukan: [judul kegiatan]

        TIDAK Sesuai
        Judul dan deskripsi berbeda dengan kegiatan yang diberikan.
        Tidak ditemukan kegiatan yang sesuai.
        """

        try:
            response = model_gem.generate_content(prompt)
            verifikasi_text = response.text
            first_line = verifikasi_text.strip().splitlines()[0].lower()

            if "ya" in first_line:
                S_search = 20
            else:
                S_search = 0

        except ResourceExhausted:
            verifikasi_text = (
                "⚠️ Verifikasi AI sementara tidak tersedia karena batas kuota tercapai.\n"
                "Silakan coba kembali beberapa saat lagi."
            )

    S_final = (
        (match_scores.get("event_name", 0)
        + match_scores.get("name", 0) + match_scores.get("organizer", 0)
        + match_scores.get("end_date", 0)) / 4
    ) * 0.8 + S_search 
    return {
        "match_scores": match_scores,
        "final_score": S_final,
        "verifikasi_ai": verifikasi_text,
        "ocr_text": final_output,
        "ocr_details": final_texts,
        "font_results": font_results,   
        "google_results": google_results
    } 
