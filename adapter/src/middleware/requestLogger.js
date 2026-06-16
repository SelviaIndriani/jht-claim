const fs = require('fs');
const path = require('path');
const { mask } = require('../utils/dataMasking');

const LOG_DIR = process.env.LOG_DIR || './logs';

// Ensure log directory exists
if (!fs.existsSync(LOG_DIR)) {
  fs.mkdirSync(LOG_DIR, { recursive: true });
}

const getLogFile = (date) => {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return path.join(LOG_DIR, `adapter-${year}-${month}-${day}.log`);
};

const formatLog = (level, message, metadata = {}) => {
  const timestamp = new Date().toISOString();
  return JSON.stringify({
    timestamp,
    level,
    message,
    ...metadata,
  }) + '\n';
};

const writeLog = (level, message, metadata = {}) => {
  const logFile = getLogFile(new Date());
  const logEntry = formatLog(level, message, metadata);

  fs.appendFile(logFile, logEntry, (err) => {
    if (err && process.env.NODE_ENV !== 'test') {
      console.error('Failed to write log:', err);
    }
  });

  // Also console log in development
  if (process.env.NODE_ENV !== 'production') {
    console.log(`[${level}] ${message}`, metadata);
  }
};

const requestLogger = (req, res, next) => {
  const startTime = Date.now();
  const requestId = req.headers['x-request-id'] || req.headers['x-request-id'] || generateRequestId();

  // Attach request ID and logger to request object
  req.requestId = requestId;
  req.logger = {
    info: (msg, meta) => writeLog('INFO', msg, { request_id: requestId, ...meta }),
    warn: (msg, meta) => writeLog('WARN', msg, { request_id: requestId, ...meta }),
    error: (msg, meta) => writeLog('ERROR', msg, { request_id: requestId, ...meta }),
    debug: (msg, meta) => writeLog('DEBUG', msg, { request_id: requestId, ...meta }),
  };

  // Log incoming request (dengan data masking)
  req.logger.info('request_received', mask({
    method: req.method,
    path: req.path,
    query: req.query,
    ip_address: req.ip || req.connection.remoteAddress,
    user_agent: req.get('user-agent'),
    content_type: req.get('content-type'),
  }));

  // Intercept response
  const originalJson = res.json.bind(res);
  res.json = function(data) {
    const duration = Date.now() - startTime;

    req.logger.info('response_sent', {
      method: req.method,
      path: req.path,
      status_code: res.statusCode,
      duration_ms: duration,
      response_size: JSON.stringify(data).length,
    });

    return originalJson(data);
  };

  // Log errors
  const originalSend = res.send.bind(res);
  res.send = function(data) {
    const duration = Date.now() - startTime;

    if (res.statusCode >= 400) {
      req.logger.warn('error_response', {
        method: req.method,
        path: req.path,
        status_code: res.statusCode,
        duration_ms: duration,
        error_message: data?.message || data,
      });
    }

    return originalSend(data);
  };

  next();
};

const generateRequestId = () => {
  return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
};

module.exports = requestLogger;
