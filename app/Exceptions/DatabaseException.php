<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Throwable;

class DatabaseException extends Exception
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render(Request $request)
    {
        $response = ['errorMessage' => $this->message];

        $previous = $this->getPrevious();
        if (config('app.env') === 'local' && isset($previous)) {
            $response = array_merge($response, [
                'error' => $previous->getMessage(),
                'trace' => $previous->getTrace(),
            ]);
        }

        return response()->json($response, $this->code === 0 ? 500 : $this->code);
    }
}
