import google.generativeai as genai

class GeminiService:
    def __init__(self, api_key):
        genai.configure(api_key=api_key)
        self.model = genai.GenerativeModel('gemini-2.5-flash')

    def verify_search_result(self, kegiatan, tanggal, penyelenggara, search_result):
        prompt = f"""
        Anda adalah AI Verifikator Dokumen.
        Kegiatan tanggal dan Penyelenggara:
        "{kegiatan}", "{tanggal}", "{penyelenggara}"
        Hasil Pencarian Google:
        Judul       : {search_result['title']}
        Deskripsi   : {search_result['description']}
        Link        : {search_result['link']}           

        Instruksi:
        - Periksa relevansi. Jawab 4 baris sesuai format:
        [YA/TIDAK]
        [Alasan singkat]
        [ADA/TIDAK DITEMUKAN]
        [Penjelasan singkat]
        """
        response = self.model.generate_content([prompt])
        return response.text
