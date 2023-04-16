<?php

namespace App\Http\Controllers;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function loginByFirebaseAction(App\RequestModel\Auth\LoginByFirebase $requestModel): App\ResponseModel\Auth\UserWithToken
    {
        try {
            $verifier = IdTokenVerifier::createWithProjectId($this->getAuthManager()->getFirebaseProjectId());
            $token = $verifier->verifyIdToken($requestModel->getIdToken());
        } catch (IdTokenVerificationFailed $exception) {
            throw new Common\Exception\ValidationFieldException('all','Invalid token');
        }

        $user = $this->getAuthManager()->getUserByFirebaseIdentifier($token->payload()['user_id']);
        $accessToken = $this->getAuthManager()->createAccessTokenByUser($user);

        return new App\ResponseModel\Auth\UserWithToken($user, $accessToken);
    }
    private function getAuthManager(): Common\Manager\Auth\AuthManager
    {
        return $this->get(Common\Manager\Auth\AuthManager::class);
    }
}
