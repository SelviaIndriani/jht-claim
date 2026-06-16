/**
 * Middleware untuk collect request metrics untuk monitoring
 */

const metrics = {
  requests: {
    total: 0,
    byMethod: {},
    byPath: {},
    byStatus: {},
  },
  latency: {
    byPath: {},
  },
  errors: {
    total: 0,
    byStatus: {},
  },
};

const metricsMiddleware = (req, res, next) => {
  const startTime = Date.now();

  // Count total requests
  metrics.requests.total++;

  // Count by method
  metrics.requests.byMethod[req.method] = (metrics.requests.byMethod[req.method] || 0) + 1;

  // Count by path
  metrics.requests.byPath[req.path] = (metrics.requests.byPath[req.path] || 0) + 1;

  // Intercept response to track metrics
  const originalJson = res.json.bind(res);
  res.json = function(data) {
    const duration = Date.now() - startTime;

    // Track status codes
    metrics.requests.byStatus[res.statusCode] = (metrics.requests.byStatus[res.statusCode] || 0) + 1;

    // Track latency by path
    if (!metrics.latency.byPath[req.path]) {
      metrics.latency.byPath[req.path] = { total: 0, count: 0, min: Infinity, max: 0, avg: 0 };
    }
    const pathMetrics = metrics.latency.byPath[req.path];
    pathMetrics.total += duration;
    pathMetrics.count++;
    pathMetrics.min = Math.min(pathMetrics.min, duration);
    pathMetrics.max = Math.max(pathMetrics.max, duration);
    pathMetrics.avg = pathMetrics.total / pathMetrics.count;

    // Track errors
    if (res.statusCode >= 400) {
      metrics.errors.total++;
      metrics.errors.byStatus[res.statusCode] = (metrics.errors.byStatus[res.statusCode] || 0) + 1;

      req.logger?.info('metric_error_recorded', {
        path: req.path,
        status: res.statusCode,
        duration_ms: duration,
      });
    }

    return originalJson(data);
  };

  next();
};

const getMetrics = () => {
  return {
    ...metrics,
    uptime_seconds: process.uptime(),
    memory_usage_mb: Math.round(process.memoryUsage().heapUsed / 1024 / 1024),
  };
};

const resetMetrics = () => {
  metrics.requests = { total: 0, byMethod: {}, byPath: {}, byStatus: {} };
  metrics.latency = { byPath: {} };
  metrics.errors = { total: 0, byStatus: {} };
};

module.exports = {
  metricsMiddleware,
  getMetrics,
  resetMetrics,
};
