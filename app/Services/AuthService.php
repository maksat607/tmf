<?php

namespace App\Services;

use App\Exceptions\AccessDeniedException;
use App\Models\AuthAccessToken;
use App\Models\AuthUser;
use Carbon\Carbon;
use Firebase\Auth\Token\Exception\InvalidToken;
use Firebase\Auth\Token\Verifier;
use Firebase\Auth\Token\Domain\Generator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;

class AuthService
{
    private $firebaseProjectId;

    public function __construct($firebaseProjectId)
    {
        $this->firebaseProjectId = $firebaseProjectId;
    }

    public function getUserByToken(Request $request)
    {
        $accessToken = $request->header('X-Access-Token');
        if (!$accessToken) {
            throw new AuthenticationException('Access token not found');
        }
        $authAccessToken = AuthAccessToken::where('token', $accessToken)->first();
        if (!$authAccessToken) {
            throw new AccessDeniedException('Invalid access token');
        }
        return $authAccessToken->user;
    }

    public function authenticateWithFirebase($firebaseToken)
    {
        try {
            $verifier = IdTokenVerifier::createWithProjectId($this->firebaseProjectId);
            $token = $verifier->verifyIdToken($firebaseToken);
        } catch (IdTokenVerificationFailed $exception) {
            throw new AuthenticationException('Invalid Firebase token');
        }

        $firebaseUserId = $token->payload()['user_id'];
        $user = AuthUser::where('firebase_identifier', $firebaseUserId)->first();

        if (!$user) {
            $user = AuthUser::create([
                'firebase_identifier' => $firebaseUserId,
                'increases_count' => 0,
                'decreases_count' => 0,
            ]);
        }

        $accessToken = $this->generateAccessToken($user);

        return ['user' => $user, 'access_token' => $accessToken];
    }

    private function generateAccessToken(AuthUser $user)
    {
        $token = new Generator();
        $accessToken = $token->create($user->toArray(), [
            'exp' => Carbon::now()->addYear()->timestamp,
            'iat' => Carbon::now()->timestamp,
        ]);

        $authAccessToken = $user->accessTokens()->create([
            'expired_at' => Carbon::now()->addYear(),
            'token' => $accessToken
        ]);

        return $authAccessToken->token;
    }

    public function validateFirebaseToken($firebaseToken)
    {
        try {
            $verifier = new Verifier($this->firebaseProjectId);
            $verifiedToken = $verifier->verifyIdToken($firebaseToken);
            $verifiedToken->getClaims();
        } catch (InvalidToken $e) {
            throw new AuthenticationException('Invalid Firebase token');
        }
    }
}
