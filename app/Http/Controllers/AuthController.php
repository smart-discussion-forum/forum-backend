<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register', [
            'groups' => Group::orderBy('name')->get(),
        ]);
    }

    public function profile()
    {
        return view('auth.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('success', 'Password updated.');
    }

        public function register(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|in:student,lecturer,admin',
                'accepted_terms' => 'required',
                'group_ids' => 'required|array|min:1',
                'group_ids.*' => 'exists:groups,id',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => $data['role'],
                'status' => \App\Enums\StatusEnum::Active,
                'last_active' => now(),
            ]);
            $user->groups()->attach($data['group_ids'],['joined_at' => now()]);

        auth()->login($user);
        session(['api_token' => $user->createToken('web_token')->plainTextToken]);

        return redirect('/dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $request->user()->update(['last_active' => now()]);
            session(['api_token' => $request->user()->createToken('web_token')->plainTextToken]);

            return redirect('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            return response()->json([
                'success' => true,
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'invalid credentials',
        ], 401);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
