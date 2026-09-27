<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $roleName = $request->role;
        if ($roleName === 'Admin' && !Role::where('name', 'Admin')->exists()) {
            $roleName = 'Tenant Admin';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ], [
            'name.required' => 'ব্যবহারকারীর নাম আবশ্যক।',
            'email.required' => 'ইমেইল এড্রেস আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল এড্রেস প্রদান করুন।',
            'email.unique' => 'এই ইমেইলটি ইতিমধ্যে নিবন্ধিত রয়েছে। অন্য ইমেইল ব্যবহার করুন।',
            'password.required' => 'পাসওয়ার্ড প্রদান করা আবশ্যক।',
            'password.min' => 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।',
            'role.required' => 'রোল নির্বাচন করা আবশ্যক।',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify role exists or fallback
        $roleObj = Role::where('name', $roleName)->first() 
                ?? Role::where('name', $request->role)->first()
                ?? Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $user->assignRole($roleObj->name);

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যবহারকারী সফলভাবে তৈরি করা হয়েছে।',
            'data' => $user->load('roles'),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = User::with('roles')
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $roleName = $request->role;
        if ($roleName === 'Admin' && !Role::where('name', 'Admin')->exists()) {
            $roleName = 'Tenant Admin';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'ব্যবহারকারীর নাম আবশ্যক।',
            'email.required' => 'ইমেইল এড্রেস আবশ্যক।',
            'email.email' => 'সঠিক ইমেইল এড্রেস প্রদান করুন।',
            'email.unique' => 'এই ইমেইলটি ইতিমধ্যে ব্যবহৃত হয়েছে।',
            'password.min' => 'নতুন পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।',
            'role.required' => 'রোল নির্বাচন করা আবশ্যক।',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update($request->only('name', 'email', 'phone', 'address'));

        if ($request->has('password') && !empty($request->password)) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $roleObj = Role::where('name', $roleName)->first() 
                ?? Role::where('name', $request->role)->first()
                ?? Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

        $user->syncRoles([$roleObj->name]);

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যবহারকারী সফলভাবে আপডেট করা হয়েছে।',
            'data' => $user->load('roles'),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === $request->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'আপনি নিজের একাউন্ট মুছতে পারবেন না।',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ব্যবহারকারী মুছে ফেলা হয়েছে।',
        ]);
    }
    
    public function getRoles()
    {
        // For simplicity, we only allow certain roles to be assigned by tenants
        return response()->json([
            'status' => 'success',
            'data' => ['Admin', 'Manager', 'Staff'],
        ]);
    }
}
