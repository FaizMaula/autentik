# from fastapi import APIRouter, UploadFile, File, Form
# from app.services.ocr_service import OCRService
# from app.services.google_search import GoogleSearchService
# from app.services.verification import hitung_skor_akhir
# from app.services.gemini_service import GeminiService
# from app.utils.date_utils import generate_date_variations, normalize_date
# import shutil
# from pathlib import Path
# import json

# router = APIRouter()

# @router.post("/verify")
# async def verify_certificate(
#     file: UploadFile = File(...),
#     nama: str = Form(...),
#     kegiatan: str = Form(...),
#     tanggal: str = Form(...),
#     penyelenggara: str = Form(...)
# ):
#     # Upload file
#     upload_dir = Path("uploads")
#     upload_dir.mkdir(exist_ok=True)
#     file_path = upload_dir / file.filename
#     with open(file_path, "wb") as buffer:
#         shutil.copyfileobj(file.file, buffer)

#     # Tanggal
#     tanggal_variations = generate_date_variations(tanggal)
#     tanggal_normalized = normalize_date(tanggal)

#     # OCR
#     targets = {"kegiatan": kegiatan, "nama": nama, "tanggal": tanggal_variations, "penyelenggara": penyelenggara}
#     ocr_service = OCRService()
#     ocr_result = ocr_service.run_ocr(str(file_path), targets)

#     # Google Search
#     gsearch = GoogleSearchService(api_key="API_KEY", cse_id="CSE_ID")
#     query = f" Seminar {kegiatan} {tanggal_normalized} {penyelenggara}"
#     top_result = gsearch.search(query)

#     # Skor Akhir
#     S_search = 20
#     S_gamma = 10
#     score = hitung_skor_akhir(
#         ocr_result["match_scores"]["kegiatan"],
#         ocr_result["match_scores"]["nama"],
#         ocr_result["match_scores"]["penyelenggara"],
#         ocr_result["match_scores"]["tanggal"],
#         S_search, S_gamma
#     )

#     # Gemini (opsional)
#     gemini = GeminiService(api_key="GEMINI_API_KEY")
#     gemini_result = gemini.verify_search_result(kegiatan, tanggal_normalized, penyelenggara, top_result)

#     return {
#         "ocr_result": ocr_result,
#         "google_result": top_result,
#         "score": score,
#         "gemini_verification": gemini_result
#     }
