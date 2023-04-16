<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\ExpiredToken;
use Kreait\Firebase\Exception\Auth\InvalidToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class VerifyFirebaseToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $serviceAccount = ServiceAccount::fromJsonFile(config('firebase.json_credentials_path'));
            $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                ->create();

            $verifiedToken = $firebase->getAuth()->verifyIdToken($token);

            $request->attributes->add(['firebase_token' => $verifiedToken]);

            return $next($request);
        } catch (InvalidToken $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (ExpiredToken $e) {
            return response()->json(['error' => 'Token expired'], 401);
        }
    }
}
