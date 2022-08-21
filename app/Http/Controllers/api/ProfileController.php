<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        try {
            $user = auth()->user();

            return response()->success(new UserResource($user));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        try {
            $request->validated();

            $user = User::find(auth()->user()->id);

            if (!$user) {
                return response()->error('User not found', 404);
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return response()->success(new UserResource($user));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $request->validated();
            $user = User::find(auth()->user()->id);
            if (!$user) {
                return response()->error('User not found', 404);
            }
            /* confirm password old */
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->error('The old password is incorrect', 422);
            }
            /* change password */
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            return response()->success(new UserResource($user));
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
    }
}
