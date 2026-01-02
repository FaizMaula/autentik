FROM python:3.11-slim

# Install System Dependencies
RUN apt-get update && apt-get install -y \
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

# Python environment
ENV OMP_NUM_THREADS=1
ENV MKL_NUM_THREADS=1

RUN python -m pip install --upgrade pip setuptools wheel

WORKDIR /app

# Copy & Install requirements
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy source code
COPY . .

# Tambahkan baris ini: Berikan izin folder /app ke appuser
RUN useradd -m appuser && chown -R appuser:appuser /app
USER appuser

EXPOSE 8001

CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8001", "--workers", "1"]