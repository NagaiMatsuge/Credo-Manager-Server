<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
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
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json([
                'data' => [],
                'message' => "Not found",
                'success' => false,
                'error' => true,
                'status_code' => 405
            ]);
        }

        if ($exception instanceof NotFoundHttpException && $request->wantsJson()) {
            return response()->json([
                'data' => [],
                'message' => "Page Not found",
                'success' => false,
                'error' => true,
                'status_code' => 404
            ]);
        }

        if ($exception instanceof ValidationException && $request->wantsJson()) {
            return response()->json([
                'data' => [],
                'message' => $exception->errors(),
                'success' => false,
                'error' => true,
                'status_code' => $exception->status
            ]);
        }

        if ($exception instanceof QueryException && $request->wantsJson()) {
            return response()->json([
                'data' => [],
                'message' => 'Wrong Input Details are given',
                'success' => false,
                'error' => true,
                'status_code' => 422
            ]);
        }

        if ($exception instanceof MethodNotAllowedHttpException && $request->wantsJson()) {
            return response()->json([
                'data' => [],
                'message' => 'Method now allowed',
                'success' => false,
                'error' => true,
                'status_code' => 405
            ]);
        }

        if ($exception instanceof HttpException && $request->wantsJson()) {
            if ($exception->getStatusCode() == 401) {
                return response()->json([
                    'data' => [],
                    'message' => 'Unauthorized client',
                    'success' => false,
                    'error' => true,
                    'status_code' => 401
                ]);
            }
        }
        return parent::render($request, $exception);
    }
}
