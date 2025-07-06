

# Presensi-PBL

Sistem Presensi Project-Based Learning (PBL) berbasis web untuk D3 Teknik Informatika & S.Tr. Teknologi Rekayasa Komputer, Jurusan Teknik Elektro, Politeknik Negeri Semarang.

## Tech Stack

- **Laravel 11** (PHP 8.2) — Backend utama
- **MySQL/MariaDB** — Database
- **Vite** — Asset bundler
- **Python 3** — Integrasi Machine Learning
- **YOLO** — Image classifier (presensi berbasis foto)
- **Docker** — Containerization

## Fitur Utama

- Presensi otomatis berbasis foto (YOLO)
- Manajemen user, role, dan hak akses (RBAC)
- Import/export data siswa/guru (Excel)
- Dashboard statistik presensi
- API untuk integrasi eksternal

## Instalasi & Setup

1. **Clone repository**
   ```
   git clone <repo-ini> presensi-pbl
   cd presensi-pbl
   ```
2. **Install dependency Laravel**
   ```
   composer install
   ```
3. **Copy file environment**
   ```
   cp .env.example .env
   ```
4. **Install dependency frontend**
   ```
   npm install
   ```
5. **Buat database & konfigurasi .env**
   - Edit file `.env` sesuai database lokal Anda
6. **Generate key & migrate database**
   ```
   php artisan key:generate
   php artisan migrate --seed
   ```
7. **Jalankan server Laravel**
   ```
   php artisan serve
   ```
8. **Jalankan image-classifier (YOLO) [opsional]**
   ```
   cd image-classifier
   pip install -r requirements.txt
   python app.py
   ```
   Atau gunakan Docker:
   ```
   docker compose up --build
   ```

## Login Default

```
username: superadmin@gmail.com
password: adminadmin
```

## Struktur Utama

- `app/` — Source code Laravel
- `image-classifier/` — Service Python YOLO
- `public/` — Public assets
- `routes/` — Routing Laravel
- `resources/` — Blade, JS, CSS

## Lisensi

Modifikasi dari Project: https://github.com/mjumain/RBAC-LARAVEL-9
