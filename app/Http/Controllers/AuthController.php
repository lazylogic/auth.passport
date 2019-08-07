<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware( 'signed' )->only( 'verify' );
        $this->middleware( 'throttle:6,1' )->only( 'verify', 'resend' );
    }

    public function signup( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ] );

        if ( $validator->fails() ) {
            return response()->json( [
                'message' => 'The given data was invalid.',
                'errors'  => $validator->errors(),
            ], 400 );
        }

        $user = new User( [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make( $request->password ),
        ] );
        $user->save();

        $user->sendEmailVerificationNotification();

        return response()->json( [
            'message' => 'Successfully created user!',
        ], 201 );
    }

    public function login( Request $request )
    {
        // 회원 email, password 확인
        if ( ! Auth::attempt( $request->only( 'email', 'password' ) ) ) {
            return response()->json( [
                'message' => 'Unauthorized',
            ], 401 );
        }

        // access_token 발급
        $user        = Auth::user();
        $tokenResult = $user->createToken( 'Personal Access Token' );
        $token       = $tokenResult->token;

        // remember_me가 설정 되지 않은 경우, 유효시간을 짧게 설정
        // ToDO : remember_me 설정
        if ( ! $request->filled( 'remember_me' ) ) {
            $token->expires_at = Carbon::now()->addDay(); # 토큰 유효 기간은 원하는 대로 지정
            $token->save();
        }

        return response()->json( [
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ] );
    }

    public function auth( Request $request )
    {
        return response()->json( Auth::user() );
    }

    public function verify( Request $request )
    {
        if ( ! ( $user = User::find( $request->get( 'id' ) ) ) ) {
            throw new AuthorizationException();
        }

        if ( $user->hasVerifiedEmail() ) {
            return response()->json( $user );
        }

        if ( $user->markEmailAsVerified() ) {
            event( new Verified( $user ) );
        }

        return response()->json( $user );
    }

    public function logout( Request $request )
    {
        Auth::user()->token()->revoke();
        return response()->json( [
            'message' => 'Successfully logged out',
        ] );
    }
}