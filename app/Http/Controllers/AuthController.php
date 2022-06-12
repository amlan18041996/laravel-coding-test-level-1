<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => [
                'required', 
                'min:8', 
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ],
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();
        if(!$user){
            return $this->sendError($user, 'Email not found.');
        }

        // Check password
        if(!Hash::check($fields['password'], $user->password)){
            return $this->sendError([], 'Password does not match the respective email.');
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = ['user' => $user, 'token' => $token];

        return $this->sendResponse($response, 'User login successfully.');
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|min:5',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required', 
                'min:8', 
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/', 
                'confirmed'
            ],
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = ['user' => $user, 'token' => $token];

        return $this->sendResponse($response, 'User registered successfully.');
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return $this->sendResponse([], 'User logout successfully.');
    }
}
