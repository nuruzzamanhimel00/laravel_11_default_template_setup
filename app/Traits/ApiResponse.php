<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Format the API response consistently.
     *
     * @param bool $success The status of the response
     * @param mixed $data The data to include in the response
     * @param string $message The message for the response
     * @param int $statusCode The HTTP status code for the response
     * @return JsonResponse The formatted JSON response
     */
    protected function respond(bool $success, $data = null, string $message = '', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data instanceof ResourceCollection
                ? $data->response()->getData(true)
                : $data;
        }

        return Response::json($response, $statusCode);
    }
    /**
     * Return a success response.
     *
     * @param mixed $data The data to include in the response
     * @param string $message The success message
     * @param int $statusCode The HTTP status code
     * @return JsonResponse
     */
    public function success($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return $this->respond(true, $data, $message, $statusCode);
    }

     /**
     * Return a created resource response.
     *
     * @param mixed $data The data to include in the response
     * @param string $message The success message
     * @return JsonResponse
     */
    public function created($data = null, string $message = 'Created'): JsonResponse
    {
        return $this->respond(true, $data, $message, 201);
    }


    /**
     * Return a no content response.
     *
     * @param string $message The success message
     * @return JsonResponse
     */
    public function noContent(string $message = 'No Content'): JsonResponse
    {
        return $this->respond(true, null, $message, 204);
    }

    /**
     * Return an error response.
     *
     * @param string $message The error message
     * @param mixed $data Additional error details
     * @param int $statusCode The HTTP status code
     * @return JsonResponse
     */
    public function error(string $message = 'Error', $data = null, int $statusCode = 500): JsonResponse
    {
        return $this->respond(false, $data, $message, $statusCode);
    }


    /**
     * Return a not found response.
     *
     * @param string $message The not found message
     * @param mixed $data Additional error details
     * @return JsonResponse
     */
    public function notFound(string $message = 'Not Found', $data = null): JsonResponse
    {
        return $this->respond(false, $data, $message, 404);
    }

     /**
     * Return a validation error response.
     *
     * @param string $message The validation error message
     * @param mixed $errors The validation errors
     * @return JsonResponse
     */
    public function validationError(string $message = 'Validation Error', $errors = null): JsonResponse
    {
        return $this->respond(false, $errors, $message, 422);
    }

    /**
     * Return an unauthorized response.
     *
     * @param string $message The unauthorized message
     * @param mixed $data Additional error details
     * @return JsonResponse
     */
    public function unauthorized(string $message = 'Unauthorized', $data = null): JsonResponse
    {
        return $this->respond(false, $data, $message, 401);
    }

     /**
     * Return a forbidden response.
     *
     * @param string $message The forbidden message
     * @param mixed $data Additional error details
     * @return JsonResponse
     */
    public function forbidden(string $message = 'Forbidden', $data = null): JsonResponse
    {
        return $this->respond(false, $data, $message, 403);
    }

    /**
     * Validate request data and return validation errors if any.
     *
     * @param Request $request The request to validate
     * @param array $rules The validation rules
     * @param array $messages Custom error messages
     * @param array $attributes Custom attribute names
     * @return JsonResponse|null Returns validation error response or null if validation passes
     */
    public function validate(Request $request, array $rules, array $messages = [], array $attributes = []): ?JsonResponse
    {
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return $this->validationError('The provided data was invalid', $validator->errors());
        }

        return null;
    }


    /**
     * Return a response based on a status code.
     *
     * @param mixed $data The data or response object
     * @return JsonResponse
     */
    public function withCode($data): JsonResponse
    {
        if (!is_object($data)) {
            return $this->error('Invalid response format');
        }

        $code = $data->code ?? 500;
        $message = $data->message ?? '';
        $responseData = isset($data->data) ? $data->data : null;

        return match ((int)$code) {
            200 => $this->success($responseData, $message),
            201 => $this->created($responseData, $message),
            204 => $this->noContent($message),
            401 => $this->unauthorized($message, $responseData),
            403 => $this->forbidden($message, $responseData),
            404 => $this->notFound($message, $responseData),
            422 => $this->validationError($message, $responseData),
            default => $this->error($message, $responseData, (int)$code),
        };
    }

    // json response
    public function jsonResponse($status=true, $message="success", $data=[], $statusCode=200)
    {
        return $this->formatResponse($status, $message, $data, $statusCode);
    }
}
