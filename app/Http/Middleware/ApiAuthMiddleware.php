<?php

namespace App\Http\Middleware;

use App\Models\UserErm;
use Illuminate\Support\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");
        $authenticate = true;

        if (!$token) {
            $authenticate = false;
        }

        $user = UserErm::where('id', $token)->first();
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
