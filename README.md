# Monorepo: frontend (Laravel) + backend (API)

Monorepo ini memakai struktur:

-   `frontend/` — Aplikasi Laravel (Blade, resources, routes, dsb.)
-   `backend/` — Layanan API terpisah (belum dibuat; placeholder)

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

Backend (API):

-   Belum tersedia. Nantinya akan dibuat di folder `backend/` (misal Laravel API/Node/NestJS/Go). Kami akan menambahkan panduan begitu ditentukan stack-nya.

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
