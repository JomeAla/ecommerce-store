<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Admin Authentication Controller
 *
 * Handles admin login, logout and authentication logic
 *
 * @author E-commerce Starter Kit
 * @version 1.0.0
 */
class AdminAuthController extends Controller
{
    /**
     * Display the admin login form.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLogin(Request $request)
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle an incoming admin login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:1'],
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $result = $this->attemptLogin($email, $password);

        if ($result['success']) {
            $admin = $result['admin'];
            $admin->last_login_at = now();
            $admin->save();

            session([
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'admin_role' => $admin->role,
            ]);

            session()->flash('success', 'Welcome back, ' . $admin->name . '!');

            return redirect()->route('admin.dashboard');
        }

        session()->flash('error', 'Invalid email or password. Please try again.');

        return redirect()->back()->withInput($request->except('password'));
    }

    /**
     * Log the admin out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $adminName = session('admin_name', 'Admin');

        session()->forget(['admin_id', 'admin_name', 'admin_role']);

        session()->flash('info', 'You have been logged out successfully.');

        return redirect()->route('admin.login');
    }

    /**
     * Attempt to log the user in with the given credentials.
     *
     * @param string $email
     * @param string $password
     * @return array{success: bool, admin: \App\Models\AdminUser|null}
     */
    private function attemptLogin(string $email, string $password): array
    {
        $adminClass = 'App\\Models\\AdminUser';

        if (!class_exists($adminClass)) {
            $adminClass = 'App\\AdminUser';
        }

        if (!class_exists($adminClass)) {
            return ['success' => false, 'admin' => null];
        }

        $admin = null;
        try {
            $admin = $adminClass::where('email', $email)->first();
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return ['success' => false, 'admin' => null];
        }

        if (!$admin) {
            Log::info('Login failed: Admin not found for email ' . $email);
            return ['success' => false, 'admin' => null];
        }

        if (!Hash::check($password, $admin->password)) {
            Log::info('Login failed: Password mismatch for email ' . $email);
            return ['success' => false, 'admin' => null];
        }

        return ['success' => true, 'admin' => $admin];
    }
}