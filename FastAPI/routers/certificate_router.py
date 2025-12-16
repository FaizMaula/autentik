from fastapi import APIRouter, UploadFile, Form, File
from datetime import date
import shutil, os
from model.ocr_verifier import process_certificate

router = APIRouter(prefix="/certificate", tags=["Certificate Verification"])


@router.post("/verify")
async def verify_certificate(
    nama: str = Form(...),
    tahun_akademik: str = Form(None),
    penyelenggara: str = Form(...),
    tanggal_mulai: str = Form(...), 
    tanggal_selesai: str = Form(...),
    nama_kegiatan: str = Form(...),
    nama_kegiatan_inggris: str = Form(None),
    file: UploadFile = File(..., alias="berkas")
):

    # simpan file sementara
    temp_path = f"temp_{file.filename}"
    with open(temp_path, "wb") as buffer:
        shutil.copyfileobj(file.file, buffer)

    berkas = file.filename

    result = process_certificate(
        nama,
        tahun_akademik,
        penyelenggara,
        tanggal_mulai,
        tanggal_selesai,
        nama_kegiatan,
        nama_kegiatan_inggris,
        berkas,
        temp_path
    )

    os.remove(temp_path)
    return result
