/**
 * Wrapper for async route handlers with error handling and logging
 */
const asyncHandler = (fn) => {
  return (req, res, next) => {
    Promise.resolve(fn(req, res, next)).catch((error) => {
      const duration = Date.now() - req.startTime;

      req.logger.error('route_handler_error', {
        route: req.path,
        method: req.method,
        error_message: error.message,
        error_stack: error.stack,
        duration_ms: duration,
      });

      res.status(error.statusCode || 500).json({
        success: false,
        message: error.message || 'Internal server error',
        request_id: req.requestId,
      });
    });
  };
};

module.exports = asyncHandler;
