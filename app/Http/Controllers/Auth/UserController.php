<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRatingUpdateType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreAuthUser;
use App\Http\Resources\Auth\AccessTokenResource;
use App\Http\Resources\Auth\AuthUserResource;
use App\Models\AuthAccessToken;
use App\Models\AuthUser;
use Carbon\Carbon;
use Firebase\Auth\Token\Verifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return new AuthUserResource($user->loadMissing(['photo', 'accessTokens']));
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
    public function destroy()
    {
//        return auth()->user()->accessTokens;
        auth()->user()->accessTokens()->delete();
        return response()->noContent();
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

//        $user = AuthUser::create([
//            'firebase_identifier' => $firebase_identifier,
//            'increases_count' => 0,
//            'decreases_count' => 0,
//        ]);
//
//        $token = sprintf('%d%s', $user->id, uniqid() . bin2hex(openssl_random_pseudo_bytes(16)));
//
//        $authAccessToken = $user->accessTokens()->create([
//            'expired_at'=>  Carbon::now()->addYear(),
//            'token'=>$token
//        ]);
//        $firebase_identifier = 'FSl8UZXH7hMe1sFQjUnVbzeq4Zl2';
        $existingUser = AuthUser::where('firebase_identifier', $firebase_identifier)->first();

        $accessToken = $existingUser->accessTokens()->where('expired_at', '>', Carbon::now())->first();
        if (!$accessToken) {
            $user = AuthUser::create([
                'firebase_identifier' => $firebase_identifier,
                'increases_count' => 0,
                'decreases_count' => 0,
            ]);

            $token = sprintf('%d%s', $user->id, uniqid() . bin2hex(openssl_random_pseudo_bytes(16)));

            $authAccessToken = $user->accessTokens()->create([
                'expired_at' => Carbon::now()->addYear(),
                'token' => $token
            ]);
        } else {
            $user = $existingUser;
            $authAccessToken = $accessToken;
        }
        return [
            "user" => new AuthUserResource($user->load('photo')),
            "accessToken" => new AccessTokenResource($authAccessToken),
            "__typename" => "Auth_UserWithToken"
        ];
    }

    public function likeAction(AuthUser $destination)
    {
        $author = auth()->user();
        if ($destination->id === $author->id) {
            throw new \App\Exceptions\ValidationFieldException();
        }

        $lastVote = $this->getLastByDestinationAndAuthor($destination, $author);
        if ($lastVote && $lastVote->created_at > \Carbon\Carbon::now()->subHour()) {
            throw new \App\Exceptions\ValidationFieldException( 'You already voted less than an hour ago');
        }

        $this->increase($destination->id, $author->id);
        $destination->increases_count = $destination->increases_count + 1;
        $destination->save();
        return response()->noContent();
//        $this->sendPush($destination, $author, true);
    }

    public function decreaseAction(AuthUser $destination){
        $author = auth()->user();
        if ($destination->id === $author->id) {
            throw new \App\Exceptions\ValidationFieldException();
        }

        $lastVote = $this->getLastByDestinationAndAuthor($destination, $author);
        if ($lastVote && $lastVote->created_at > \Carbon\Carbon::now()->subHour()) {
            throw new \App\Exceptions\ValidationFieldException( 'You already voted less than an hour ago');
        }

        $this->decrease($destination->id, $author->id);
        $destination->decreases_count = $destination->decreases_count + 1;
        $destination->save();
        return response()->noContent();
    }
    public function getLastByDestinationAndAuthor(AuthUser $destination, AuthUser $author)
    {
        return DB::table('auth__user_rating_updates')
            ->where('destination_id', $destination->id)
            ->where('author_id', $author->id)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function increase($destinationId,$authorId){
        DB::table('auth__user_rating_updates')->insert([
            'destination_id' => $destinationId,
            'author_id' => $authorId,
            'created_at' => now(),
            'type' => UserRatingUpdateType::INCREASE
        ]);
    }
    public function decrease($destinationId,$authorId){
        DB::table('auth__user_rating_updates')->insert([
            'destination_id' => $destinationId,
            'author_id' => $authorId,
            'created_at' => now(),
            'type' => UserRatingUpdateType::DECREASE
        ]);
    }

}
