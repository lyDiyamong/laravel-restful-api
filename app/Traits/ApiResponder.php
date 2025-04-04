<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Pagination\LengthAwarePaginator;

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
        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => [], 'message' => "Success"], $code);
        }

        $transformer = $collection->first()->transformer;
        // Filter data
        $collection = $this->filterData($collection, $transformer);
        // Sort data
        $collection = $this->sortData($collection, $transformer);
        // Paginate
        $collection = $this->paginate($collection);
        // Cache data
        $collection = $this->cacheData($collection);

        if ($transformer) {

            $collection = $this->transformData($collection, $transformer);
        }


        return $this->successResponse([
            'data' => $collection['data'] ?? $collection,
            'pagination' => $collection['pagination'] ?? null,
            'message' => 'Success'
        ], $code);
    }

    /**
     * Return a success response with the given model
     */
    protected function showOne(Model $model, int $code = 200): JsonResponse
    {

        $transformer = $model->transformer;
        if ($transformer) {

            $model = $this->transformData($model, new $transformer)['data'];
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

    protected function sortData(Collection $collection, $transformer)
    {
        if (request()->has('sort_by')) {
            $attribute = $transformer::originalAttribute(request()->sort_by);
            $collection = $collection->sortBy->{$attribute};
        }

        return $collection;
    }

    private function filterData(Collection $collection, $transformer)
    {
        $excludedKeys = ['sort_by', 'limit', 'page'];

        foreach (request()->query() as $key => $value) {
            if (in_array($key, $excludedKeys)) continue;

            $attribute = $key;

            if ($transformer) {
                $attribute = $transformer::originalAttribute($key);
            }

            if (isset($attribute, $value)) {
                $collection = $collection->where($attribute, $value);
            }
        }

        return $collection;
    }


    private function paginate(Collection $collection, int $perPage = 15): LengthAwarePaginator
    {

        $rules = [
            'limit' => "integer|min:2|max:50"
        ];
        if (request()->has("limit")) {
            $perPage = (int)request()->limit;
        }

        Validator::validate(request()->all(), $rules);
        $page = LengthAwarePaginator::resolveCurrentPage(); // Get current page from request (?page=)
        $total = $collection->count();                      // Total items
        $results = $collection->forPage($page, $perPage);   // Get items for current page



        return new LengthAwarePaginator(
            $results->values(), // Reindex the items
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),           // Keeps the current URL
                'query' => request()->query(),        // Keeps other query strings like ?sort_by=name
            ]
        );
    }

    private function cacheData($data)
    {
        $url = request()->fullUrl();

        return Cache::remember($url, 10, function () use ($data) {
            return $data;
        });
    }

    private function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer)->toArray();

        if ($data instanceof LengthAwarePaginator) {
            return [
                'data' => $transformation['data'],
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ]
            ];
        }

        return $transformation;
    }

    protected function setRefreshCookie(string $key, string $value)
    {
        return cookie(
            $key,
            $value,
            60 * 24 * 30, // minutes (30 days)
            '/',
            null,
            false,
            true // HttpOnly
        );
    }
}
