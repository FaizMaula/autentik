# Monorepo: frontend (Laravel) + backend (API)

Monorepo ini memakai struktur:

-   `frontend/` — Aplikasi Laravel (Blade, resources, routes, dsb.)
   Folder ini merupakan source code utama yang dideploy ke Railway
-   `FASTAPI/` — Layanan FastAPI terpisah (dideploy sebagai container di Hugging Face Spaces)

## Struktur Direktori
├── frontend/      # Aplikasi Laravel (frontend web)
└── FASTAPI/       # Backend API (FastAPI)


## Development

Frontend (Laravel):

1. Dari root repo:
    ```bash
    cd frontend
    composer install
    php artisan key:generate
    php artisan migrate
    php artisan serve
    ```
2. Aplikasi berjalan di http://127.0.0.1:8000
Pada environment Railway, perintah serve tidak digunakan karena aplikasi dijalankan melalui web server (Nginx/Apache) sesuai konfigurasi Railway.

Backend (API):

-   Mengambil API dari file Folder FastAPI
-   Backend API diambil dari folder FASTAPI
-   API tidak dideploy ke Railway
-   API dijalankan terpisah sebagai container di Hugging Face Spaces
-   Frontend Laravel akan mengakses API ini melalui HTTP request menggunakan endpoint yang dikonfigurasi di .env

## Build aset (opsional, jika memakai Vite di Laravel)

Jalankan dari folder `frontend/`:

```bash
npm install
npm run build
```

Hasil build umumnya berada di `frontend/public/build` dan disajikan Laravel via `/build/*`.

## Catatan

-   Berkas environment ada di `frontend/.env` (diabaikan Git).
-   `frontend/vendor/`, `frontend/public/build/`, dan `frontend/node_modules/` diabaikan Git.
-   README Laravel lama dipindahkan menjadi `frontend/README-LARAVEL.md`.
