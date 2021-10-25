<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class MazeException extends Exception
{
    public function render($request, Exception $exception)
    {
        return response()->json(
            [
                'message' => $exception->getMessage(),
                'data' => date('Y-m-d H:i:s', now()->timestamp),    // imprime a data para encontrar no log
                'Exception' => 'MazeException'
            ],
            $exception->getCode() == 0 ? 500 : $exception->getCode()
        );
    }
}
