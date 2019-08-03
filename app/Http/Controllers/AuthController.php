<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup( Request $request )
    {
        return phpinfo();

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

        if ( $request->filled( 'remember_me' ) ) {
            $token->expires_at = Carbon::now()->addWeeks( 1 ); # 토큰 유효 기간은 원하는 대로 지정
        }

        $token->save();

        return response()->json( [
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ] );
    }

    public function me( Request $request )
    {
        return response()->json( Auth::user() );
    }

    public function logout( Request $request )
    {
        Auth::user()->token()->revoke();
        return response()->json( [
            'message' => 'Successfully logged out',
        ] );
    }
}