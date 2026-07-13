<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Display the central Home Portal view.
     */
    public function home()
    {
        $user = Auth::user();
        if ($user->is_active === false) {
            return view('home');
        }

        if ($user->hasPermission('admin_panel')) {
            return redirect(route('admin.index'));
        } elseif ($user->hasPermission('kitchen_terminal')) {
            return redirect(route('orders.kitchen'));
        } elseif ($user->hasPermission('waiter_terminal')) {
            return redirect(route('orders.index'));
        }

        return view('home');
    }

    /**
     * Display the registration view.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pending', // Default role; admin can alter role access levels later
        ]);

        Auth::login($user);

        return redirect(route('home'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'));
    }

    /**
     * Update the authenticated user's email and password.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['required', 'string'],
            'password' => ['nullable', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        // Check if current password is valid
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'current_password' => ['The provided password does not match your current password.']
                ]
            ], 422);
        }

        // Update email
        $user->email = $request->email;

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    /**
     * Show the user profile page.
     */
    public function showProfile()
    {
        return view('profile');
    }

    /**
     * Update the authenticated user's email and name info.
     */
    public function updateProfileInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile information updated successfully!'
        ]);
    }

    /**
     * Update the authenticated user's password.
     */
    public function updateProfilePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        // Check if current password is valid
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'current_password' => ['The provided password does not match your current password.']
                ]
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }
}
