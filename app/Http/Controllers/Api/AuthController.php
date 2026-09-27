<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        Log::info('Login attempt from mobile app:', $request->only('email', 'phone', 'device_name'));

        $request->validate([
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $login = trim($request->input('login') ?? $request->input('phone') ?? $request->input('email') ?? '');
        if (empty($login)) {
            throw ValidationException::withMessages([
                'email' => ['মোবাইল নম্বর বা ইমেল প্রদান করুন।'],
            ]);
        }
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $user = User::where('email', $login)->first();
        } else {
            // Check direct phone
            $user = User::where('phone', $login)->first();

            // Check BD variants (+880, 880, 01)
            if (! $user) {
                $variants = [];
                if (str_starts_with($login, '+880')) {
                    $variants[] = '0' . substr($login, 4);
                } elseif (str_starts_with($login, '880')) {
                    $variants[] = '0' . substr($login, 3);
                } elseif (str_starts_with($login, '01')) {
                    $variants[] = '+88' . $login;
                    $variants[] = '88' . $login;
                }
                if (! empty($variants)) {
                    $user = User::whereIn('phone', $variants)->first();
                }
            }

            // Fallback by email
            if (! $user) {
                $user = User::where('email', $login)->first();
            }
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['প্রদানকৃত তথ্য আমাদের রেকর্ডের সাথে মিলছে না।'], // Bengali error message
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'tenant_id' => 1,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        return \DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('Admin');

            $token = $user->createToken('Mobile App')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tenant_id' => 1,
                ],
            ], 201);
        });
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(), // Using Spatie roles
                'tenant' => $user->tenant,
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email'));

        return response()->json([
            'status' => 'success',
            'message' => 'প্রোফাইল তথ্য সফলভাবে আপডেট করা হয়েছে।',
            'data' => $user,
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['বর্তমান পাসওয়ার্ডটি সঠিক নয়।'],
            ]);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।',
        ]);
    }

    public function updateTenantSettings(Request $request)
    {
        $tenant = $request->user()->tenant;

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string',
            'invoice_conditions' => 'nullable|string',
        ]);

        $tenant->update($request->only('name', 'address', 'phone', 'invoice_conditions'));

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যবসার তথ্য সফলভাবে আপডেট করা হয়েছে।',
            'data' => $tenant,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'সফলভাবে লগআউট করা হয়েছে।',
        ]);
    }
}
