<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        ValidationException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return  response()->json(['message' => trans('exception.unauthenticated')], 401);
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);

        // dd($exception);
        // if ($exception instanceof ModelNotFoundException) {
        //     return  response()->json(
        //         ['message' => trans(
        //             'exception.not_found',
        //             ['resource' => trans('exception.resource.' . $exception->getModel())]
        //         )],
        //         404
        //     );
        // }

        // $response = ['errorMessage' => trans('exception.internal_error')];

        // if (config('app.env') === 'local') {
        //     $response = array_merge($response, [
        //         'error' => $exception->getMessage(),
        //         'trace' => $exception->getTrace(),
        //     ]);
        // }

        // return response()->json($response,  500);
    }
}
