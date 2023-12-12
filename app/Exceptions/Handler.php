<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (Exception $e, Request $request) {
            if ($e instanceof NotFoundHttpException) {
                return $this->handleNotFound($request);
            }
            if ($e instanceof ModelNotFoundException) {
                return $this->handleModelNotFound($request);
            }
            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->handleMethodNotAllowed($request);
            }
            if ($this->isHttpException($e)) {
                return $this->handleInternalServerError($request);
            }
        });
    }

    protected function handleNotFound(Request $request)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'error' => [
                    'pesan' => 'URL TIDAK DITEMUKAN'
                ]
            ], 404);
        }
    }

    protected function handleModelNotFound(Request $request)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'error' => [
                    'pesan' => 'DATA TIDAK DITEMUKAN'
                ]
            ], 404);
        }
    }

    protected function handleMethodNotAllowed(Request $request)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'error' => [
                    'pesan' => 'METHOD TIDAK DIIZINKAN'
                ]
            ], 405);
        }
    }

    protected function handleInternalServerError(Request $request)
    {
        if ($request->is('api/*')) {
            return response()->json([
                'error' => [
                    'pesan' => 'TERJADI MASALAH PADA SERVER'
                ]
            ], 500);
        }
    }
}
