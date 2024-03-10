<?php

namespace Migunov\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseService
{
    public static function json(
        array $data,
        array $params = [],
        int $status = Response::HTTP_OK
    ): JsonResponse {
        $meta = ['timestamp' => time()];

        if (isset($data['meta'])) {
            $meta = array_merge($meta, $data['meta']);
            unset($data['meta']);
        }

        if ($params) {
            $meta['params'] = $params;
        }

        if (isset($data['data'])) {
            $d = $data['data'];
            unset($data['data']);
            $meta = array_merge($meta, $data);
            $data = $d;
        }

        return response()->json(
            [
                'meta' => $meta,
                'data' => $data,
            ],
            $status
        );
    }
}
