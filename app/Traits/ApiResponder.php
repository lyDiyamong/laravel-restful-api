<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;

trait ApiResponder
{
    /**
     * Return a success JSON response
     */
    protected function successResponse(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * Return an error JSON response
     */
    protected function errorResponse(string $message, int $code): JsonResponse
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    /**
     * Return a success response with the given data
     */
    protected function showAll(Collection $collection, int $code = 200): JsonResponse
    {
        return $this->successResponse(['data' => $collection, 'message' => "Success"], $code);
    }

    /**
     * Return a success response with the given model
     */
    protected function showOne(Model $model, int $code = 200): JsonResponse
    {
        return $this->successResponse(['data' => $model, 'message' => "Success"], $code);
    }

    /**
     * Return a success message
     */
    protected function showMessage(string $message, int $code = 200): JsonResponse
    {
        return $this->successResponse(['message' => $message], $code);
    }
} 