# 🏦 JHT Claim — BPJS Ketenagakerjaan

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Vue](https://img.shields.io/badge/Nuxt.js-3-00DC82?style=flat-square&logo=nuxt.js&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js-Express-339933?style=flat-square&logo=node.js&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)

> Sistem pengajuan Klaim Jaminan Hari Tua (JHT) secara online untuk peserta BPJS Ketenagakerjaan yang mengundurkan diri atau habis masa kontrak — tanpa perlu antri di kantor.

---

## 📌 Tentang Proyek

Aplikasi ini memungkinkan peserta BPJS Ketenagakerjaan untuk mengajukan klaim JHT secara digital melalui form multi-step yang terverifikasi langsung ke database kepesertaan. Setelah pengajuan berhasil, peserta menerima email konfirmasi otomatis beserta nomor klaim untuk tracking status.

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────┐
│                  FRONTEND                           │
│           Nuxt.js 3 + Tailwind CSS                  │
│              http://localhost:3000                   │
└──────────────────────┬──────────────────────────────┘
                       │ HTTP Request
┌──────────────────────▼──────────────────────────────┐
│                  ADAPTER                            │
│           Node.js / Express.js                      │
│    Rate Limiting · CORS · Request Forwarding        │
│              http://localhost:4000                   │
└──────────────────────┬──────────────────────────────┘
                       │ HTTP Request
┌──────────────────────▼──────────────────────────────┐
│                  BACKEND                            │
│                 Laravel 11                          │
│   Controllers (thin) · Services · FormRequest       │
│   Mail · Activity Log · File Storage               │
│              http://localhost:8000                   │
└──────────────────────┬──────────────────────────────┘
                       │ MySQL
┌──────────────────────▼──────────────────────────────┐
│                  DATABASE                           │
│                   MySQL 8                           │
│              jht_claim_db                           │
└─────────────────────────────────────────────────────┘
```

---

## 🛠️ Tech Stack

| Layer | Teknologi | Keterangan |
|---|---|---|
| **Frontend** | Nuxt.js 3 + Tailwind CSS | Multi-step form, validasi client-side |
| **Adapter** | Node.js + Express.js | Middleware: rate limiting, CORS, routing |
| **Backend** | Laravel 11 + PHP 8.3 | REST API, service layer, validasi server-side |
| **Database** | MySQL 8 | Data peserta, klaim, kantor cabang, layanan |
| **Email** | Mailtrap (dev) | Konfirmasi klaim via SMTP |
| **Activity Log** | Spatie Laravel Activitylog | Audit trail setiap pengajuan |

---

## ✨ Fitur Utama

### Frontend
- 🪜 **Multi-step form** — 4 tahap pengajuan yang terstruktur
- 🔍 **Verifikasi real-time** — cek kombinasi Nomor BPJS + NIK ke database
- 📋 **Auto-fill** — data peserta otomatis terisi setelah verifikasi berhasil
- 🛡️ **Validasi NIK** — format 16 digit + kode wilayah
- 📎 **Upload dokumen** — e-KTP dan Pas Foto (JPG/PNG, maks 2MB)
- 👁️ **Review step** — konfirmasi semua data sebelum submit
- ✅ **Halaman sukses** — tampilkan nomor klaim yang bisa di-tracking

### Backend
- 🔒 **Service layer** — business logic terpisah dari controller (KlaimJhtService, PesertaService)
- 📝 **FormRequest validation** — validasi terstruktur sebelum masuk ke controller
- 🗂️ **Validasi silang** — data form dicocokkan dengan data kepesertaan di DB
- 📧 **Email otomatis** — konfirmasi klaim dikirim ke email peserta
- 🗃️ **Activity log** — setiap pengajuan tercatat lengkap dengan IP address
- 🗑️ **Soft delete** — data tidak benar-benar terhapus dari database

### Adapter
- 🚦 **Rate limiting** — 100 req/15 menit (umum), 10 klaim/jam (submit)
- 🔐 **Helmet.js** — security headers otomatis
- 🌐 **CORS** — hanya mengizinkan request dari frontend

---

## 📂 Struktur Proyek

```
jht-claim/
├── frontend/                   # Nuxt.js 3
│   ├── pages/
│   │   └── index.vue           # Halaman utama form klaim
│   ├── components/ui/
│   │   ├── AlertMessage.vue
│   │   ├── FileUpload.vue
│   │   ├── FormField.vue
│   │   └── StepIndicator.vue
│   ├── composables/
│   │   └── useApi.js           # HTTP client ke adapter
│   └── utils/
│       └── validators.js       # Validasi NIK, BPJS, dll
│
├── adapter/                    # Node.js / Express
│   └── src/
│       ├── index.js            # Entry point, middleware setup
│       └── routes/
│           ├── klaim.js
│           ├── peserta.js
│           └── referensi.js
│
└── backend/                    # Laravel 11
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/Api/
    │   │   │   ├── KlaimJhtController.php
    │   │   │   ├── PesertaController.php
    │   │   │   └── ReferensiController.php
    │   │   └── Requests/
    │   │       └── KlaimJhtRequest.php
    │   ├── Services/
    │   │   ├── KlaimJhtService.php  # Business logic klaim
    │   │   └── PesertaService.php   # Business logic peserta
    │   ├── Models/
    │   │   ├── KlaimJht.php
    │   │   ├── PesertaBpjs.php
    │   │   ├── KantorCabang.php
    │   │   └── Layanan.php
    │   └── Mail/
    │       └── KlaimJhtMail.php
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    └── routes/
        └── api.php
```

---

## 🔌 API Endpoints

Base URL: `http://localhost:8000/api`

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/referensi/kantor-cabang` | Daftar kantor cabang aktif |
| `GET` | `/referensi/layanan` | Daftar jenis layanan JHT |
| `POST` | `/peserta/verifikasi` | Verifikasi No. BPJS + NIK |
| `POST` | `/klaim` | Submit pengajuan klaim |
| `GET` | `/klaim/{no_klaim}` | Cek status klaim |
| `GET` | `http://localhost:4000/health` | Health check adapter |

---

## 🚀 Cara Menjalankan

### Prasyarat
- PHP 8.3+, Composer
- Node.js 18+, npm
- MySQL 8

### 1. Setup Database

```sql
CREATE DATABASE jht_claim_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Backend (Laravel)

```bash
cd backend
cp .env.example .env
# Edit .env — isi DB_PASSWORD dan MAIL_USERNAME/PASSWORD

composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

▶ Backend: `http://localhost:8000`

### 3. Adapter (Node.js)

```bash
cd adapter
cp .env.example .env
# Edit .env — isi LARAVEL_API_URL=http://localhost:8000/api

npm install
npm start
```

▶ Adapter: `http://localhost:4000`

### 4. Frontend (Nuxt.js)

```bash
cd frontend
npm install
npm run dev
```

▶ Frontend: `http://localhost:3000`

---

## 📧 Konfigurasi Email (Mailtrap)

1. Daftar / login di [mailtrap.io](https://mailtrap.io)
2. Buka **Inboxes → SMTP Settings**
3. Salin credentials ke `backend/.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

---

## 🧪 Sample Data Peserta

Gunakan data berikut untuk testing verifikasi di form:

| No. BPJS | NIK | Nama |
|---|---|---|
| `10000000001` | `3171234501890001` | Budi Santoso |
| `10000000002` | `3273015502900002` | Siti Rahayu |
| `10000000003` | `3578016703850003` | Ahmad Fauzi |
| `10000000004` | `1271014504920004` | Dewi Lestari |
| `10000000005` | `3404016205880005` | Eko Prasetyo |

---

## 🌿 Branch Strategy

| Branch | Tujuan |
|---|---|
| `main` | Production-ready — hanya diupdate via merge dari `staging` |
| `staging` | Development — semua perubahan masuk sini dulu |

Alur pengembangan:
```
feature branch → staging → (review) → main
```

---

## 📋 Status Klaim

| Status | Keterangan |
|---|---|
| `pending` | Baru diajukan, menunggu diproses |
| `diproses` | Sedang dalam proses verifikasi |
| `disetujui` | Klaim disetujui |
| `ditolak` | Klaim ditolak |
