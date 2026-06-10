const express = require('express');
const axios = require('axios');
const router = express.Router();

const LARAVEL_API = process.env.LARAVEL_API_URL;

// GET /api/referensi/kantor-cabang — fetch list of branch offices
router.get('/kantor-cabang', async (req, res) => {
  try {
    const response = await axios.get(`${LARAVEL_API}/referensi/kantor-cabang`);
    res.json(response.data);
  } catch (error) {
    const status = error.response?.status || 500;
    const message = error.response?.data?.message || 'Failed to fetch branch office data.';
    res.status(status).json({ success: false, message });
  }
});

// GET /api/referensi/layanan — fetch list of available services
router.get('/layanan', async (req, res) => {
  try {
    const response = await axios.get(`${LARAVEL_API}/referensi/layanan`);
    res.json(response.data);
  } catch (error) {
    const status = error.response?.status || 500;
    const message = error.response?.data?.message || 'Failed to fetch service data.';
    res.status(status).json({ success: false, message });
  }
});

module.exports = router;
