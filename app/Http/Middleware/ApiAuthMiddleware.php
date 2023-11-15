<?php

namespace App\Http\Middleware;

use App\Models\UserModel;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");
        $authenticate = true;

        if (!$token) {
            $authenticate = false;
        }

        $user = UserModel::where('id', $token)->first();
        if (!$user || $user->expired_at <= Carbon::now()) {
            $authenticate = false;
        } else {
            Auth::login($user);
        }

        if ($authenticate) {
            return $next($request);
        } else {
            return response()->json([
                "error" => [
                    "pesan" => "UNAUTHORIZED"
                ]
            ])->setStatusCode(401);
        }
    }
}
