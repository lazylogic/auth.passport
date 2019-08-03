<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup( Request $request )
    {
        // $request->validate( [
        //     'name'     => 'required|string',
        //     'email'    => 'required|string|email|unique:users',
        //     'password' => 'required|string|confirmed',
        // ] );

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
            'password' => bcrypt( $request->password ),
        ] );
        $user->save();

        return response()->json( [
            'message' => 'Successfully created user!',
        ], 201 );
    }

    public function login( Request $request )
    {
        //
    }

    public function logout( Request $request )
    {
        //
    }
}