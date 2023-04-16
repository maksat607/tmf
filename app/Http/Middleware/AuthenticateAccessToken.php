<?php

namespace App\Http\Middleware;

use App\Models\AuthAccessToken;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


use Illuminate\Foundation\Exceptions\Handler;


class AuthenticateAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->header('X-Access-Token');

        if (!$accessToken) {
            return response()->json(['error' => 'access_token_required'], 401);
        }

        $accessTokenModel = AuthAccessToken::where([
            ['token', $accessToken],
            ['expired_at', '>', now()],
        ])->first();

        if (!$accessTokenModel) {
            return response()->json(['error' => 'invalid_access_token'], 401);
        }

        $user = $accessTokenModel->user;

        Auth::login($user);

        return $next($request);
    }


}
