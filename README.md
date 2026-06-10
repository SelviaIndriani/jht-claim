# Aplikasi Klaim JHT — BPJS Ketenagakerjaan

Aplikasi pengajuan Klaim Jaminan Hari Tua (JHT) untuk peserta yang mengundurkan diri atau habis masa kontrak.

## Tech Stack

| Layer | Teknologi |
|---|---|
| Frontend | Nuxt.js 3 + Tailwind CSS |
| Adapter | Node.js (Express) |
| Backend/API | Laravel 11 |
| Database | MySQL |
| Email | Mailtrap (dev) |

## Arsitektur

```
Frontend (Nuxt :3000)
    ↓ HTTP
Adapter (Node.js/Express :4000)
    ↓ HTTP
Backend (Laravel :8000)
    ↓ MySQL
Database (jht_claim_db)
```

## Cara Menjalankan

### 1. Setup Database MySQL

```sql
CREATE DATABASE jht_claim_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Backend (Laravel)

```bash
cd backend

# Salin dan sesuaikan .env
cp .env.example .env

# Edit .env: isi DB_PASSWORD dan MAIL_USERNAME/PASSWORD (Mailtrap)

# Install dependencies
composer install

# Generate key
php artisan key:generate

# Jalankan migration + seeder
php artisan migrate --seed

# Jalankan server
php artisan serve
```

Backend berjalan di: `http://localhost:8000`

### 3. Adapter (Node.js)

```bash
cd adapter
npm install
node src/index.js
```

Adapter berjalan di: `http://localhost:4000`

### 4. Frontend (Nuxt.js)

```bash
cd frontend
npm install
npm run dev
```

Frontend berjalan di: `http://localhost:3000`

---

## Setup Mailtrap

1. Daftar/login ke [mailtrap.io](https://mailtrap.io)
2. Buka **Inboxes → SMTP Settings**
3. Salin `Username` dan `Password`
4. Isi di `backend/.env`:
   ```
   MAIL_USERNAME=your_mailtrap_username
   MAIL_PASSWORD=your_mailtrap_password
   ```

---

## Sample Data Peserta (untuk testing)

| No. BPJS | NIK | Nama |
|---|---|---|
| 10000000001 | 3171234501890001 | Budi Santoso |
| 10000000002 | 3273015502900002 | Siti Rahayu |
| 10000000003 | 3578016703850003 | Ahmad Fauzi |
| 10000000004 | 1271014504920004 | Dewi Lestari |
| 10000000005 | 3404016205880005 | Eko Prasetyo |

---

## Fitur

- ✅ Form multi-step (4 tahap)
- ✅ Validasi NIK Indonesia (format + kode provinsi)
- ✅ Validasi Nomor BPJS
- ✅ Verifikasi kombinasi BPJS + NIK ke database
- ✅ Auto-fill data peserta setelah verifikasi
- ✅ Auto-select jenis layanan sesuai kepesertaan
- ✅ Upload e-KTP dan Pas Foto (JPG/PNG, max 2MB)
- ✅ Pilihan konfirmasi: Video Call / Datang ke Kantor
- ✅ Dropdown kantor cabang dari database (dikelompokkan per provinsi)
- ✅ Review & konfirmasi sebelum submit
- ✅ Notifikasi email dengan summary lengkap (Mailtrap)
- ✅ Halaman sukses dengan nomor klaim
- ✅ Node.js Adapter sebagai middleware
- ✅ Rate limiting pada adapter
- ✅ Validasi server-side lengkap di Laravel
