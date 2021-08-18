<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ValidationException::class,
        // ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ApplicationException) {
            return response([
                'success' => false,
                'status' => 400,
                'message' => $exception->getMessage()
            ], 400);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response([
                'success' => false,
                'status' => 404,
                'message' => 'Not Found HTTP Exception',
                'response' => (array) $exception,
            ], 404);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response([
                'success' => false,
                'status' => 404,
                'message' => 'Method Not Allowed HTTP Exception',
                'response' => (array) $exception,
            ], 404);
        }
        if ($exception instanceof ModelNotFoundException) {
            return response([
                'success' => false,
                'status' => 404,
                'message' => 'Model Not Found Exception.',
                'response' => (array) $exception,
            ], 404);
        }
        return parent::render($request, $exception);
    }
}
