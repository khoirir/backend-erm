<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
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
            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->handleMethodNotAllowed($request);
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
}
