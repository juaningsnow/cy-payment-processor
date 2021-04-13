<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';
        $request->merge([
            $login_type => $request->input('login')
        ]);
        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        if (Auth::attempt($request->only($login_type, 'password'))) {
            return redirect()->intended($this->redirectPath());
        }

        return redirect()->back()
            ->withInput()
            ->withErrors([
                'login' => 'These credentials do not match our records.',
            ]);
        // $user = User::where('email', $request->email)->first();
        // if ($user) {
        //     if (Hash::check($request->password, $user->password)) {
        //         $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        //         $response = ['token' => $token];
        //         return response($response, 200);
        //     } else {
        //         $response = ["message" => "Password mismatch"];
        //         return response($response, 422);
        //     }
        // } else {
        //     $response = ["message" =>'User does not exist'];
        //     return response($response, 422);
        // }
    }
}
