<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use League\OAuth2\Server\Exception\OAuthServerException;

class CustomApiExceptionHandler extends ExceptionHandler
{
    use ApiResponse;
    public function render($request, Throwable $exception)
    {

        if ($request->is('api/*') || $request->wantsJson()) {
            return $this->handleApiException($exception, $request);
        }

        // Use Laravel's default exception handling for non-API requests
        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions and return consistent JSON responses
     */
    private function handleApiException(Throwable $exception, $request)
    {
        // Handle validation errors
        if ($exception instanceof ValidationException) {
            return $this->validationError(
                $exception->getMessage(),
                $exception->errors()
            );
        }

        // Handle authentication errors
        if ($exception instanceof AuthenticationException) {
            return $this->unauthorized('Unauthenticated or Invalid API key');
        }

        // Handle authorization errors
        if ($exception instanceof AccessDeniedHttpException) {
            return $this->forbidden('You do not have permission to access this resource');
        }

        // Handle rate limiting
        if ($exception instanceof ThrottleRequestsException) {
            return $this->error('Too many requests', null, 429);
        }

        // Handle 404 errors
        if ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
            return $this->notFound('Resource not found');
        }

        // Handle method not allowed errors
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->error('Method not allowed', null, 405);
        }

        // Handle all other errors
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        $message = $exception->getMessage() ?: 'Server Error';

        if (config('app.debug')) {
            $data = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
            return $this->error($message, $data, $statusCode);
        }

        if ($exception instanceof OAuthServerException) {
            return $this->error('Token has expired. Please refresh the token.', null, 401);
        }

        return $this->error('Server Error', null, $statusCode);
    }
}
