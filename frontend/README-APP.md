# Backend (Root Folder)

Folder ini disiapkan sebagai tempat untuk resource/arsitektur backend terpisah (misal dokumentasi, script deploy/CI, atau service backend berbeda) di luar struktur Laravel default.

Catatan:
- Git tidak melacak folder kosong. Dengan adanya README ini, folder `backend/` akan tampil di GitHub.
- Untuk kode Laravel (controller, routes, view) backend yang sudah ada, lokasinya mengikuti konvensi Laravel:
  - Controller: `app/Http/Controllers/Backend/`
  - Views: `resources/views/backend/`
  - Routes: `routes/backend.php`

Silakan isi folder ini sesuai kebutuhan (misal `docs/`, `scripts/`, atau konfigurasi khusus backend).
