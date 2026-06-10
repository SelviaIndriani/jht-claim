<x-mail::message>
# Konfirmasi Pengajuan Klaim JHT

Yth. **{{ $klaim['nama_lengkap'] }}**,

Pengajuan klaim JHT Anda telah berhasil diterima. Berikut adalah ringkasan data klaim yang telah Anda ajukan:

---

## Informasi Klaim

| Keterangan | Detail |
|---|---|
| **No. Klaim** | {{ $klaim['no_klaim'] }} |
| **Status** | Menunggu Proses |
| **Tanggal Pengajuan** | {{ \Carbon\Carbon::parse($klaim['submitted_at'])->locale('id')->translatedFormat('d F Y, H:i') }} WIB |

---

## Data Peserta

| Keterangan | Detail |
|---|---|
| **No. BPJS Ketenagakerjaan** | {{ $klaim['no_bpjs'] }} |
| **NIK** | {{ substr($klaim['nik'], 0, 4) }}xxxxxxxxxx{{ substr($klaim['nik'], -2) }} |
| **Nama Lengkap** | {{ $klaim['nama_lengkap'] }} |
| **Nama Ibu Kandung** | {{ $klaim['nama_ibu_kandung'] ?? '-' }} |
| **Tempat, Tanggal Lahir** | {{ $klaim['tempat_lahir'] }}, {{ \Carbon\Carbon::parse($klaim['tanggal_lahir'])->locale('id')->translatedFormat('d F Y') }} |
| **Email** | {{ $klaim['email'] }} |

---

## Detail Klaim

| Keterangan | Detail |
|---|---|
| **Sebab Klaim** | {{ $klaim['sebab_klaim'] === 'mengundurkan_diri' ? 'Mengundurkan Diri' : 'Berakhir Kontrak' }} |

---

## Jenis Layanan yang Diajukan

@foreach($klaim['layanan'] as $layanan)
- ✅ **{{ $layanan['nama'] }}** — {{ $layanan['deskripsi'] }}
@endforeach

---

## Cara Konfirmasi Klaim

@if($klaim['cara_konfirmasi'] === 'video_call')
**Video Call**

Petugas kami akan menghubungi Anda melalui video call untuk proses verifikasi. Pastikan email **{{ $klaim['email'] }}** aktif dan dapat dihubungi.
@else
**Datang ke Kantor Cabang**

Silakan datang ke kantor cabang berikut untuk proses verifikasi:

> **{{ $klaim['kantor_cabang']['nama'] }}**
> {{ $klaim['kantor_cabang']['alamat'] }}, {{ $klaim['kantor_cabang']['kota'] }}, {{ $klaim['kantor_cabang']['provinsi'] }}
> Telp: {{ $klaim['kantor_cabang']['telepon'] }}

Harap membawa dokumen asli (KTP, Kartu BPJS Ketenagakerjaan) saat datang ke kantor.
@endif

---

<x-mail::panel>
**Informasi Penting:**
Proses verifikasi klaim JHT membutuhkan waktu **3–7 hari kerja** setelah semua dokumen dinyatakan lengkap dan valid. Anda akan mendapatkan notifikasi email mengenai perkembangan status klaim.
</x-mail::panel>

Jika Anda memiliki pertanyaan, hubungi kami melalui:
- 📞 **Hotline:** 175
- 🌐 **Website:** [sso.bpjsketenagakerjaan.go.id](https://sso.bpjsketenagakerjaan.go.id)
- 📧 **Email:** care@bpjsketenagakerjaan.go.id

Salam,
**BPJS Ketenagakerjaan**
</x-mail::message>
