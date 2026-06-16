require('dotenv').config();

const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const rateLimit = require('express-rate-limit');
const fileValidation = require('./middleware/fileValidation');
const requestLogger = require('./middleware/requestLogger');
const { metricsMiddleware, getMetrics } = require('./middleware/metrics');

const referenceRoutes = require('./routes/referensi');
const memberRoutes = require('./routes/peserta');
const claimRoutes = require('./routes/klaim');

const app = express();
const PORT = process.env.PORT || 4000;

// Security headers
app.use(helmet());

// CORS — allow only Nuxt frontend
app.use(cors({
  origin: ['http://localhost:3000'],
  methods: ['GET', 'POST'],
  allowedHeaders: ['Content-Type', 'Accept', 'X-Request-ID'],
  credentials: true,
}));

// Request logging (custom + morgan) + metrics
app.use(requestLogger);
app.use(metricsMiddleware);
app.use(morgan('combined'));

// Body parser
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// General rate limiter — 100 requests per 15 minutes
const limiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 100,
  message: { success: false, message: 'Too many requests. Please try again after 15 minutes.' },
});

// Stricter limiter for claim submissions — 10 requests per hour
const claimLimiter = rateLimit({
  windowMs: 60 * 60 * 1000,
  max: 10,
  message: { success: false, message: 'Too many claim submissions. Please try again after 1 hour.' },
});

app.use('/api', limiter);

// Routes
app.use('/api/referensi', referenceRoutes);
app.use('/api/peserta', memberRoutes);
app.use('/api/klaim', claimLimiter, fileValidation, claimRoutes);

// Health check endpoint dengan metrics
app.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    service: 'JHT Claim Adapter',
    timestamp: new Date().toISOString(),
    version: '1.0.0',
    uptime: process.uptime(),
  });
});

// Metrics endpoint (untuk monitoring)
app.get('/metrics', (req, res) => {
  res.json(getMetrics());
});

// 404 handler
app.use((req, res) => {
  res.status(404).json({ success: false, message: 'Endpoint not found.' });
});

// Global error handler
app.use((err, req, res, next) => {
  console.error('Adapter Error:', err.message);
  res.status(500).json({ success: false, message: 'An internal server error occurred.' });
});

app.listen(PORT, () => {
  console.log(`✅ JHT Claim Adapter running at http://localhost:${PORT}`);
  console.log(`🔗 Laravel API: ${process.env.LARAVEL_API_URL}`);
});
