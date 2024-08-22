<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseTrait
{
    public function success(array $data = [], ?string $message = null): JsonResponse
    {
        return new JsonResponse([
            'status'  => true,
            'message' => $message ?? __('success'),
            'data'    => $data,
        ], 200);
    }

    public function error(
        ?string $message = null,
        array $data = [],
        int $status = Response::HTTP_BAD_REQUEST,
        ?bool $root = false
    ): JsonResponse {
        $arr = [
            'status'  => false,
            'message' => $message ?? __('error'),
        ];

        if ($root) {
            $arr = array_merge($arr, $data);
        } else {
            $arr['data'] = $data;
        }

        return new JsonResponse($arr, $status);
    }
}
