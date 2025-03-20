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
    protected function errorResponse(string | array $message, int $code): JsonResponse
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    /**
     * Return a success response with the given data
     */
    protected function showAll(Collection $collection, int $code = 200): JsonResponse
    {
        if ($collection->isEmpty()){
            return $this->successResponse(['data' => [], 'message' => "Success"], $code);
        }

        $transformer = $collection->first()->transformer;
        if ($transformer) {

            $collection = $this->transformData($collection, $transformer);
        }

        return $this->successResponse(['data' => $collection, 'message' => "Success"], $code);
    }

    /**
     * Return a success response with the given model
     */
    protected function showOne(Model $model, int $code = 200): JsonResponse
    {

        $transformer = $model->transformer;
        if ($transformer) {

            $model = $this->transformData($model, new $transformer);
        }
        return $this->successResponse(['data' => $model, 'message' => "Success"], $code);
    }

    /**
     * Return a success message
     */
    protected function showMessage(string $message, int $code = 200): JsonResponse
    {
        return $this->successResponse(['message' => $message], $code);
    }

    private function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();

    }
} 