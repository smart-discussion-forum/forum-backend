<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return back()->withInput()->withErrors(['email' => 'No account was found with that email address.']);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->setRememberToken(Str::random(60));

        $user->save();

        return redirect()->route('login')->with('status', 'Password updated. You can now log in with your new password.');
    }

        public function register(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|in:student,Lecturer,Admin',
                'accepted_terms' => 'required',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role' => $data['role'],
                'status' => \App\Enums\StatusEnum::Active,
                'last_active' => now(),
            ]);

        auth()->login($user);
        session(['api_token' => $user->createToken('web_token')->plainTextToken]);

        return redirect()->route('groups.index');
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

    public function apiRegister(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'required|in:student,Lecturer,Admin',
        'accepted_terms' => 'required',
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        'role' => $data['role'],
        'status' => \App\Enums\StatusEnum::Active,
        'last_active' => now(),
    ]);

    return response()->json([
        'success' => true,
        'token' => $user->createToken('auth_token')->plainTextToken,
        'user' => $user,
    ]);
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
