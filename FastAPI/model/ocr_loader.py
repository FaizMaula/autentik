import torch
from transformers import TrOCRProcessor, VisionEncoderDecoderModel
import easyocr

device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

# === Load ONCE ===
processor = TrOCRProcessor.from_pretrained(
    "microsoft/trocr-base-printed",
    use_fast=False
)

model = VisionEncoderDecoderModel.from_pretrained(
    "microsoft/trocr-base-printed"
).to(device)

# EasyOCR reader (load sekali)
reader = easyocr.Reader(['en'], gpu=torch.cuda.is_available())
