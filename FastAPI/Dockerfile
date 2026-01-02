FROM python:3.11-slim

# 1. Install System Dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    ca-certificates \
    curl \
    poppler-utils \
    libglib2.0-0 \
    libsm6 \
    libxext6 \
    libxrender1 \
    libgomp1 \
    libgl1 \
    libglvnd0 \
    && rm -rf /var/lib/apt/lists/*

# 2. Setup User UID 1000 (Standar Hugging Face)
# Hugging Face mengharuskan user dengan UID 1000
RUN useradd -m -u 1000 user
USER user
ENV HOME=/home/user \
    PATH=/home/user/.local/bin:$PATH \
    PYTHONUNBUFFERED=1 \
    PYTHONDONTWRITEBYTECODE=1 \
    OMP_NUM_THREADS=1 \
    MKL_NUM_THREADS=1

# Set working directory di dalam home user
WORKDIR $HOME/app

# 3. Install Python Dependencies
# Upgrade pip & install Torch CPU secara eksplisit agar image tidak bengkak
RUN pip install --no-cache-dir --upgrade pip setuptools wheel && \
    pip install --no-cache-dir torch torchvision --index-url https://download.pytorch.org/whl/cpu

# Copy requirements dan install
COPY --chown=user requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# 4. Copy Source Code
# Pastikan folder ini berisi isi dari folder FastAPI Anda
COPY --chown=user . $HOME/app

# 5. Konfigurasi Port
# Hugging Face MEWAJIBKAN port 7860
EXPOSE 7860

# Jalankan uvicorn di port 7860
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "7860", "--workers", "1"]