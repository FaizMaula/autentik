from pydantic import BaseModel
from typing import List

class VerificationRequest(BaseModel):
    nama: str
    kegiatan: str
    tanggal: str  # string dd/mm/yyyy
    penyelenggara: str
    tanggal_variations: List[str] = None  # optional, bisa di-generate di backend
