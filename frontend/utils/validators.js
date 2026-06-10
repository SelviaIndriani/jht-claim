/**
 * Validasi NIK Indonesia (Nomor Induk Kependudukan)
 *
 * Format: 16 digit
 * - Digit 1–2  : kode provinsi (11–96)
 * - Digit 3–4  : kode kab/kota
 * - Digit 5–6  : kode kecamatan
 * - Digit 7–12 : tanggal lahir (DDMMYY); perempuan DD + 40
 * - Digit 13–16: nomor urut (tidak boleh 0000)
 */
export function validateNIK(nik) {
  if (!nik) return { valid: false, message: 'NIK wajib diisi.' }

  const nikStr = nik.toString().replace(/\s/g, '')

  if (!/^\d+$/.test(nikStr)) {
    return { valid: false, message: 'NIK hanya boleh berisi angka.' }
  }

  if (nikStr.length !== 16) {
    return { valid: false, message: `NIK harus 16 digit. Saat ini ${nikStr.length} digit.` }
  }

  // Validasi kode provinsi (digit 1–2)
  const kodeProvinsi = parseInt(nikStr.substring(0, 2))
  const kodeProvinsValid = [
    11, 12, 13, 14, 15, 16, 17, 18, 19, 21,
    31, 32, 33, 34, 35, 36,
    51, 52, 53,
    61, 62, 63, 64, 65,
    71, 72, 73, 74, 75, 76,
    81, 82,
    91, 92, 94,
  ]

  if (!kodeProvinsValid.includes(kodeProvinsi)) {
    return { valid: false, message: 'Kode provinsi pada NIK tidak valid.' }
  }

  // Validasi tanggal lahir (digit 7–12: DDMMYY)
  const tglStr = nikStr.substring(6, 12)
  let dd = parseInt(tglStr.substring(0, 2))
  const mm = parseInt(tglStr.substring(2, 4))

  // Perempuan: DD di-offset +40
  if (dd > 40) dd -= 40

  if (dd < 1 || dd > 31) {
    return { valid: false, message: 'Tanggal lahir yang tersimpan di NIK tidak valid.' }
  }
  if (mm < 1 || mm > 12) {
    return { valid: false, message: 'Bulan lahir yang tersimpan di NIK tidak valid.' }
  }

  // Nomor urut tidak boleh 0000
  const nomorUrut = nikStr.substring(12, 16)
  if (nomorUrut === '0000') {
    return { valid: false, message: 'Nomor urut pada NIK tidak valid.' }
  }

  return { valid: true, message: '' }
}

/**
 * Validasi Nomor Peserta BPJS Ketenagakerjaan (KPJ)
 * Format: tepat 11 digit angka
 */
export function validateNoBPJS(noBpjs) {
  if (!noBpjs) return { valid: false, message: 'Nomor KPJ wajib diisi.' }

  const str = noBpjs.toString().replace(/\s/g, '')

  if (!/^\d+$/.test(str)) {
    return { valid: false, message: 'Nomor KPJ hanya boleh berisi angka.' }
  }

  if (str.length !== 11) {
    return { valid: false, message: `Nomor KPJ harus 11 digit. Saat ini ${str.length} digit.` }
  }

  return { valid: true, message: '' }
}

/**
 * Validasi format alamat email
 */
export function validateEmail(email) {
  if (!email) return { valid: false, message: 'Alamat email wajib diisi.' }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
  if (!emailRegex.test(email)) {
    return { valid: false, message: 'Format alamat email tidak valid.' }
  }

  return { valid: true, message: '' }
}

/**
 * Validasi nomor HP / WhatsApp Indonesia
 * Format yang diterima: 08xx, +628xx, 628xx
 */
export function validateNoHP(noHp) {
  if (!noHp) return { valid: false, message: 'Nomor HP wajib diisi.' }

  const cleaned = noHp.replace(/[\s\-().]/g, '')
  const regex = /^(\+62|62|0)[0-9]{8,12}$/

  if (!regex.test(cleaned)) {
    return { valid: false, message: 'Format nomor HP tidak valid. Contoh: 08123456789 atau +6281234567890' }
  }

  return { valid: true, message: '' }
}

/**
 * Validasi field wajib (tidak boleh kosong)
 */
export function validateRequired(value, namaField) {
  if (value === null || value === undefined || value === '') {
    return { valid: false, message: `${namaField} wajib diisi.` }
  }
  if (typeof value === 'string' && value.trim() === '') {
    return { valid: false, message: `${namaField} wajib diisi.` }
  }
  return { valid: true, message: '' }
}

/**
 * Validasi tipe dan ukuran file unggahan
 * Tipe yang diterima: JPG, PNG. Ukuran maks: 2 MB.
 */
export function validateFile(file, namaField = 'File') {
  if (!file) return { valid: false, message: `${namaField} wajib diunggah.` }

  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png']
  if (!allowedTypes.includes(file.type)) {
    return { valid: false, message: `${namaField} harus berformat JPG atau PNG.` }
  }

  const maxSize = 2 * 1024 * 1024 // 2 MB
  if (file.size > maxSize) {
    return { valid: false, message: `Ukuran ${namaField} tidak boleh melebihi 2 MB.` }
  }

  return { valid: true, message: '' }
}
