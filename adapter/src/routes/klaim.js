const express = require('express');
const multer = require('multer');
const axios = require('axios');
const FormData = require('form-data');
const path = require('path');
const router = express.Router();

const LARAVEL_API = process.env.LARAVEL_API_URL;

// Use memory storage so files can be forwarded directly to Laravel
const storage = multer.memoryStorage();
const upload = multer({
  storage,
  limits: { fileSize: 2 * 1024 * 1024 }, // 2 MB max per file
  fileFilter: (req, file, cb) => {
    const allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (allowedMimes.includes(file.mimetype)) {
      cb(null, true);
    } else {
      cb(new Error('Unsupported file format. Only JPG and PNG are allowed.'));
    }
  },
});

/**
 * POST /api/klaim
 * Submit a new JHT claim. Forwards multipart form data to Laravel.
 */
router.post(
  '/',
  upload.fields([
    { name: 'foto_ktp', maxCount: 1 },
    { name: 'pas_foto', maxCount: 1 },
  ]),
  async (req, res) => {
    try {
      // Build FormData payload to forward to Laravel
      const formData = new FormData();

      // Append all text fields
      const textFields = [
        'no_bpjs', 'nik', 'nama_lengkap', 'nama_ibu_kandung', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'alamat', 'email', 'no_hp', 'nama_perusahaan',
        'tanggal_masuk_kerja', 'tanggal_keluar_kerja', 'sebab_klaim',
        'cara_konfirmasi', 'kantor_cabang_id',
      ];

      textFields.forEach((field) => {
        if (req.body[field] !== undefined && req.body[field] !== null) {
          formData.append(field, req.body[field]);
        }
      });

      // Append selected services array (layanan_dipilih[])
      const selectedServices = req.body['layanan_dipilih[]'] || req.body.layanan_dipilih;
      if (selectedServices) {
        const servicesArray = Array.isArray(selectedServices) ? selectedServices : [selectedServices];
        servicesArray.forEach((id) => formData.append('layanan_dipilih[]', id));
      }

      // Append ID card photo (foto_ktp)
      if (req.files?.foto_ktp?.[0]) {
        const file = req.files.foto_ktp[0];
        formData.append('foto_ktp', file.buffer, {
          filename: `ktp_${Date.now()}${path.extname(file.originalname)}`,
          contentType: file.mimetype,
        });
      }

      // Append portrait photo (pas_foto)
      if (req.files?.pas_foto?.[0]) {
        const file = req.files.pas_foto[0];
        formData.append('pas_foto', file.buffer, {
          filename: `pasfoto_${Date.now()}${path.extname(file.originalname)}`,
          contentType: file.mimetype,
        });
      }

      const response = await axios.post(`${LARAVEL_API}/klaim`, formData, {
        headers: { ...formData.getHeaders() },
        maxBodyLength: Infinity,
      });

      res.status(response.status).json(response.data);

    } catch (error) {
      const status = error.response?.status || 500;
      const data = error.response?.data || { success: false, message: 'Failed to submit claim. Please try again.' };
      res.status(status).json(data);
    }
  }
);

/**
 * GET /api/klaim/:claimNumber
 * Check the status of an existing claim.
 */
router.get('/:claimNumber', async (req, res) => {
  try {
    const response = await axios.get(`${LARAVEL_API}/klaim/${req.params.claimNumber}`);
    res.json(response.data);
  } catch (error) {
    const status = error.response?.status || 500;
    const data = error.response?.data || { success: false, message: 'Claim data not found.' };
    res.status(status).json(data);
  }
});

// Multer error handler
router.use((err, req, res, next) => {
  if (err instanceof multer.MulterError) {
    if (err.code === 'LIMIT_FILE_SIZE') {
      return res.status(422).json({ success: false, message: 'File size exceeds the 2 MB limit.' });
    }
  }
  if (err.message) {
    return res.status(422).json({ success: false, message: err.message });
  }
  next(err);
});

module.exports = router;
