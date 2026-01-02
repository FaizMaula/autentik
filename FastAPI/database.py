import os
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

# HAPUS: from dotenv import load_dotenv
# HAPUS: load_dotenv(dotenv_path)

# Ambil langsung dari environment OS (disuntikkan oleh Docker)
# Berikan nilai default (fallback) jika variabel tidak ditemukan
DB_USER = os.getenv("DB_USERNAME", "root")
DB_PASSWORD = os.getenv("DB_PASSWORD", "root")
DB_HOST = os.getenv("DB_HOST", "mysql")  # Di Docker, host-nya adalah nama service
DB_PORT = os.getenv("DB_PORT", "3306")
DB_NAME = os.getenv("DB_DATABASE", "autentik")

print("=== ENV DEBUG (DOCKER NATIVE) ===")
print(f"DB_USER: {DB_USER}")
print(f"DB_HOST: {DB_HOST}")
print(f"DB_NAME: {DB_NAME}")
print("================================")

# Pastikan pymysql ada di requirements.txt
DATABASE_URL = f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}"

print("DATABASE_URL:", DATABASE_URL)

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()