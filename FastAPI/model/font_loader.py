import albumentations as A
import csv
import numpy as np
import onnxruntime as ort
import yaml
import os


BASE_DIR = os.path.dirname(os.path.abspath(__file__))
# === PATH LOKAL / HF ===
CONFIG_PATH  = os.path.join(BASE_DIR, "Checkpoint", "model_config.yaml")
MODEL_PATH   = os.path.join(BASE_DIR, "Checkpoint", "model.onnx")
MAPPING_PATH = os.path.join(BASE_DIR, "font-classify-main", "google_fonts_mapping.tsv")
# === Load config ===
with open(CONFIG_PATH, "r") as f:
    config = yaml.safe_load(f)

INPUT_SIZE = config["size"]
CLASSNAMES = config["classnames"]

# === Font mapping ===
google_font_mapping = {}
with open(MAPPING_PATH, "r") as f:
    reader = csv.reader(f, delimiter="\t")
    for i, row in enumerate(reader):
        if i > 0:
            filename, font_name, version = row
            google_font_mapping[filename] = (font_name, version)

# === ONNX Session ===
font_session = ort.InferenceSession(
    MODEL_PATH,
    # providers=["CUDAExecutionProvider", "CPUExecutionProvider"]
)


import cv2

def cut_max(image: np.ndarray, max_size: int = 1024):
    if image.shape[0] > max_size:
        image = image[:max_size, :, :]
    if image.shape[1] > max_size:
        image = image[:, :max_size, :]
    return image


def resize_with_pad(image: np.ndarray, size: int):
    h, w = image.shape[:2]
    scale = size / max(h, w)
    nh, nw = int(h * scale), int(w * scale)

    resized = cv2.resize(image, (nw, nh))

    canvas = np.ones((size, size, 3), dtype=np.uint8) * 255
    y0 = (size - nh) // 2
    x0 = (size - nw) // 2
    canvas[y0:y0+nh, x0:x0+nw] = resized

    return canvas


# === Transform ===
def preprocess_font_image(image_rgb: np.ndarray):
    image = cut_max(image_rgb, 1024)
    image = resize_with_pad(image, INPUT_SIZE)

    image = image.astype(np.float32)
    image /= 255.0

    image = (image - np.array([0.485, 0.456, 0.406], dtype=np.float32)) / \
            np.array([0.229, 0.224, 0.225], dtype=np.float32)

    image = np.transpose(image, (2, 0, 1))
    image = np.expand_dims(image, 0).astype(np.float32)

    return image




def softmax(x):
    e = np.exp(x - np.max(x))
    return e / e.sum()


def predict_font(image_rgb: np.ndarray):
    image = preprocess_font_image(image_rgb)

    logits = font_session.run(None, {"input": image})[0][0]
    probs = softmax(logits)

    class_id = int(probs.argmax())
    class_name = CLASSNAMES[class_id]

    return {
        "class": class_name,
        "google_font": google_font_mapping.get(class_name),
        "confidence": float(probs[class_id])
    }

