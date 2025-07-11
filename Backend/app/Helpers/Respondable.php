<?php

namespace App\Helpers;

trait Respondable
{
    public function respondError(Error $error)
    {
        return response()->json([
            'status' => 'error',
            'message' => $error->getBody()
        ], $error->getCode());
    }

    public function respondWithMessage($data, $message, $status = 200)
    {
        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        $payload = [
            'status' => $status < 400 ? 'success' : 'error',
            'message' => $message,
        ];

        if (isset($data)) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    public function respond($data, $status = 200)
    {
        if ($data instanceof Error) {
            return $this->respondError($data);
        }

        return response()->json($data, $status);
    }

    public function message($message, $status = 200)
    {
        return response()->json([
            'status' => $status < 400 ? 'success' : 'error',
            'message' => $message,
        ], $status);
    }
}
