<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'nid_photo' => 'nullable|image|max:2048',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
        ]);

        if ($request->hasFile('nid_photo')) {
            $user->addMediaFromRequest('nid_photo')->toMediaCollection('nid_photo');
        }
        if ($request->hasFile('profile_photo')) {
            $user->addMediaFromRequest('profile_photo')->toMediaCollection('profile_photo');
        }

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'নতুন স্টাফ (ইউজার) সফলভাবে যোগ করা হয়েছে।');
    }

    public function show(User $user)
    {
         //
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = $user->roles->first()->name ?? '';

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:25',
            'address' => 'nullable|string',
            'nid_photo' => 'nullable|image|max:2048',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        if ($request->hasFile('nid_photo')) {
            $user->clearMediaCollection('nid_photo');
            $user->addMediaFromRequest('nid_photo')->toMediaCollection('nid_photo');
        }
        if ($request->hasFile('profile_photo')) {
            $user->clearMediaCollection('profile_photo');
            $user->addMediaFromRequest('profile_photo')->toMediaCollection('profile_photo');
        }

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'স্টাফের তথ্য আপডেট করা হয়েছে।');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'আপনি নিজের অ্যাকাউন্ট মুছতে পারবেন না।');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'স্টাফের তথ্য মুছে ফেলা হয়েছে।');
    }
}
