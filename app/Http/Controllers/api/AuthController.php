<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse

    {
        $data = $request->validated();

        if (!auth()->attempt($data)) {
            return response()->error('Credentials Invalid', 401);
        }

        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->success([
            'user' => auth()->user(),
            'token' => $token
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->sendEmailVerificationNotification();

        return response()->success('User Register');
    }

    public function logout()
    {
        if (!auth()->user()) {
            return response()->error('Unauthenticated', 401);
        }
        auth()->user()->tokens()->delete();
        return response()->success('Logout Successfully');
    }
}