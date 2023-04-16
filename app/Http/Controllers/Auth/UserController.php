<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreAuthUser;
use App\Http\Resources\Auth\AuthUserResource;
use App\Models\AuthAccessToken;
use App\Models\AuthUser;
use Carbon\Carbon;
use Firebase\Auth\Token\Verifier;
use Illuminate\Http\Request;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use Kreait\Firebase\JWT\IdTokenVerifier;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $accessToken = $request->header('X-Access-Token');
        $user = AuthAccessToken::where('token', $accessToken)->first()?->user;
        return new AuthUserResource($user->loadMissing(['photo','accessTokens']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AuthUser $user)
    {
        return new AuthUserResource($user->loadMissing('photo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAuthUser $request)
    {
        $accessToken = $request->header('X-Access-Token');
        $user = AuthAccessToken::where('token', $accessToken)->first()?->user;
        $user->update($request->validated());
        return new AuthUserResource($user->loadMissing('photo'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function loginByFirebase(Request $request)
    {
        $firebaseToken = $request->get('idToken');

        if (!$firebaseToken) {
            return response()->json(['error' => 'firebase_access_token_required'], 401);
        }
        try {
            $verifier = IdTokenVerifier::createWithProjectId('takemyflight-e3250');
            $token = $verifier->verifyIdToken($firebaseToken);

        } catch (IdTokenVerificationFailed $exception) {
            return response()->json(['error' => $exception->getMessage()], 401);
        }

        $firebase_identifier = $token->payload()['user_id'];
        $user = AuthUser::create([
            'firebase_identifier' => $firebase_identifier,
            'increases_count' => 0,
            'decreases_count' => 0,
        ]);

        $token = sprintf('%d%s', $user->id, uniqid() . bin2hex(openssl_random_pseudo_bytes(16)));

        $authAccessToken = $user->accessTokens()->create([
            'expired_at'=>  Carbon::now()->addYear(),
            'token'=>$token
        ]);
        return new AuthUserResource($user->load(['photo','accessTokens']));
    }


}
