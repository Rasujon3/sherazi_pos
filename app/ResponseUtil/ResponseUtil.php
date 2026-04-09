<?php

namespace App\ResponseUtil;

class ResponseUtil
{
    /**
     * @param  string  $message
     * @param  mixed  $data
     * @return array
     */
    public static function makeResponse($message, $data)
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * @param  string  $message
     * @param  array  $data
     * @return array
     */
    public static function makeError($message, array $data = [])
    {
        $res = [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];

        return $res;
    }
}
