const express = require('express');
const axios = require('axios');
const router = express.Router();

const LARAVEL_API = process.env.LARAVEL_API_URL;

/**
 * POST /api/peserta/verifikasi
 * Verify combination of no_bpjs + nik against the database.
 * Body: { no_bpjs, nik }
 */
router.post('/verifikasi', async (req, res) => {
  const { no_bpjs, nik, email } = req.body;

  // Basic input presence check before forwarding to Laravel
  if (!no_bpjs || !nik || !email) {
    return res.status(422).json({
      success: false,
      message: 'BPJS number and NIK are required.',
      errors: {
        no_bpjs: !no_bpjs ? ['BPJS number is required.'] : [],
        nik: !nik ? ['NIK is required.'] : [],
        email: !email ? ['Email is required.'] : [],
      },
    });
  }

  try {
    const response = await axios.post(`${LARAVEL_API}/peserta/verifikasi`, { no_bpjs, nik, email });
    res.json(response.data);
  } catch (error) {
    const status = error.response?.status || 500;
    const data = error.response?.data || { success: false, message: 'Member verification failed.' };
    res.status(status).json(data);
  }
});

module.exports = router;
