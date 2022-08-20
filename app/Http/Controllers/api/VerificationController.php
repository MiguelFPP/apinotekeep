<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->error("Invalid/Expired url provided.", 401);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->to(env('FRONT_URL'));
    }

    public function resend()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->error("Email already verified.", 400);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->success("Email verification link sent on your email id");
    }
}
