<?php

namespace App\Exceptions;

use Exception;
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
    public function render()
    {
        $response = ['message' => $this->message];

        $previous = $this->getPrevious();
        if (config('app.debug') && isset($previous)) {
            $response = array_merge($response, [
                'error' => $previous->getMessage(),
                'trace' => $previous->getTrace(),
            ]);
        }

        return response()->json($response, $this->code === 0 ? 500 : $this->code);
    }
}
