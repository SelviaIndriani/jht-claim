const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'application/pdf'];
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

const validateFileUpload = (req, res, next) => {
  if (!req.is('multipart/form-data') && !req.headers['content-type']?.includes('multipart/form-data')) {
    return next();
  }

  const files = req.files || {};
  const errors = [];

  for (const fieldName of ['foto_ktp', 'pas_foto']) {
    const file = files[fieldName];

    if (!file) {
      continue;
    }

    // Check MIME type
    if (!ALLOWED_MIMES.includes(file.mimetype)) {
      errors.push({
        field: fieldName,
        message: `File type tidak diizinkan. Hanya JPG, PNG, dan PDF yang diizinkan.`,
        received: file.mimetype,
      });
    }

    // Check file size
    if (file.size > MAX_FILE_SIZE) {
      errors.push({
        field: fieldName,
        message: `Ukuran file terlalu besar. Maksimal 5MB, diterima ${(file.size / 1024 / 1024).toFixed(2)}MB.`,
        size: file.size,
      });
    }

    // Check filename for malicious patterns
    if (!/^[\w\s\-().]+$/.test(file.name)) {
      errors.push({
        field: fieldName,
        message: `Nama file mengandung karakter tidak diizinkan.`,
        filename: file.name,
      });
    }
  }

  if (errors.length > 0) {
    return res.status(400).json({
      success: false,
      message: 'File validation error',
      errors,
    });
  }

  next();
};

module.exports = validateFileUpload;
