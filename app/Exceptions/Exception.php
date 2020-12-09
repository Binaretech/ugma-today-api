<?php

namespace App\Exceptions;

use Exception as BaseException;
use Throwable;

class Exception extends BaseException
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
        return response()->json($this->message, $this->code === 0 ? 500 : $this->code);
    }
}
