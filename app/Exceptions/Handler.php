<?php

namespace App\Exceptions;

use App\Traits\ApiResponder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponder;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        if ($e instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($e->getModel()));
            return $this->errorResponse("No {$modelName} found with the specified identifier", 404);
        }

        if ($e instanceof AuthenticationException) {
            return $this->errorResponse('Unauthenticated', 401);
        }

        if ($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), 403);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('The specified method for the request is invalid', 405);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse('The specified URL cannot be found', 404);
        }

        if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        if ($e instanceof QueryException) {
            $errorCode = $e->errorInfo[1];
            
            // Foreign key constraint violation
            if ($errorCode == 1451) {
                return $this->errorResponse('Cannot remove this resource permanently. It is related to another resource', 409);
            }
            
            // Unique constraint violation
            if ($errorCode == 1062) {
                return $this->errorResponse('Duplicate entry. The resource already exists', 409);
            }
            
            // For PostgreSQL specific errors
            if (str_contains($e->getMessage(), 'relation') && str_contains($e->getMessage(), 'does not exist')) {
                return $this->errorResponse('Database relation error: ' . $e->getMessage(), 500);
            }
            
            return $this->errorResponse('Database error: ' . $e->getMessage(), 500);
        }

        // Only show detailed error information in debug mode
        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        return $this->errorResponse('Unexpected error. Try again later.', 500);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        
        return $this->errorResponse($errors, 422);
    }
} 