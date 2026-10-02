<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Firm;
use App\Models\User;
use App\Models\AuditLog;

class AuthController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Show Login Page
    // ─────────────────────────────────────────────────────────────
    public function showLogin()
    {
        // Already authenticated → redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        if (session('login_type') === 'firm') {
            if (session('firm_temp_authenticated')) {
                return redirect()->route('firm-selection');
            }
            if (session('firm_id')) {
                return redirect()->route('dashboard');
            }
        }

        return view('auth.login');
    }

    // ─────────────────────────────────────────────────────────────
    // Handle Login — branches on login_type (admin | firm)
    // ─────────────────────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required',
            'login_type' => 'in:admin,firm',
        ]);

        $loginType = $request->input('login_type', 'admin');

        // ══════════════════════════════════════════════════════════
        //  FIRM LOGIN
        // ══════════════════════════════════════════════════════════
        if ($loginType === 'firm') {
            return $this->firmLogin($request);
        }

        // ══════════════════════════════════════════════════════════
        //  ADMIN LOGIN (unchanged)
        // ══════════════════════════════════════════════════════════
        return $this->adminLogin($request);
    }

    // ─────────────────────────────────────────────────────────────
    // Admin Login — uses users table + Auth::attempt()
    // ─────────────────────────────────────────────────────────────
    private function adminLogin(Request $request)
    {
        $email = strtolower(trim((string)$request->input('email')));
        $password = (string)$request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email', 'login_type'))
                ->with('error', 'Invalid email or password.');
        }

        if ($user->status !== 'active') {
            return back()
                ->withInput($request->only('email', 'login_type'))
                ->with('error', 'Your account is inactive. Please contact admin.');
        }

        if (Auth::attempt(['email' => $email, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->put('login_type', 'admin');
            AuditLog::log('Auth', 'Login', 'Admin logged in: ' . $email);
            return $this->getIntendedOrDashboard();
        }

        return back()
            ->withInput($request->only('email', 'login_type'))
            ->with('error', 'Invalid email or password.');
    }

    // ─────────────────────────────────────────────────────────────
    // Firm Login — uses firms table + Hash::check()
    // ─────────────────────────────────────────────────────────────
    private function firmLogin(Request $request)
    {
        $email = strtolower(trim((string)$request->input('email')));
        $password = (string)$request->input('password');

        // Find firm by email
        $firm = Firm::where('email', $email)->first();

        // Email not found — don't reveal whether it's the email or password
        if (! $firm) {
            return back()
                ->withInput($request->only('email', 'login_type'))
                ->with('error', 'Invalid Login ID or Password.');
        }

        // Check for inactive firm BEFORE password check
        if ($firm->status !== 'active') {
            return back()
                ->withInput($request->only('email', 'login_type'))
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        // Password not set
        if (empty($firm->password)) {
            return back()
                ->withInput($request->only('email', 'login_type'))
                ->with('error', 'No password set for this account. Please contact the administrator.');
        }

        // Password mismatch
        if (! Hash::check($password, $firm->password)) {
            return back()
                ->withInput($request->only('email', 'login_type'))
                ->with('error', 'Invalid Login ID or Password.');
        }

        // ✅ Authenticated — store temporary authenticated firm session
        $request->session()->regenerate();
        session()->forget('url.intended');
        $request->session()->put([
            'login_type'              => 'firm',
            'firm_temp_authenticated' => true,
            'temp_firm_id'            => $firm->id,
            'temp_firm_name'          => $firm->firm_name,
            'firm_email'              => $firm->email,
            'firm_status'             => $firm->status,
        ]);

        AuditLog::log('Auth', 'Firm Pre-Auth', 'Firm credentials verified: ' . $firm->firm_name . ' <' . $firm->email . '>');

        return redirect()->route('firm-selection');
    }

    // ─────────────────────────────────────────────────────────────
    // Step 2 Selection Screen
    // ─────────────────────────────────────────────────────────────
    public function showFirmSelection()
    {
        if (session('login_type') !== 'firm' || !session('firm_temp_authenticated')) {
            return redirect()->route('login');
        }

        // Only allow access to the specific firm they logged in as
        $firms = Firm::where('id', session('temp_firm_id'))->where('status', 'active')->get();

        if ($firms->isEmpty()) {
            return redirect()->route('login')->with('error', 'Your firm account is inactive or not found.');
        }

        // Active financial years
        $financialYears = \App\Models\FinancialYear::where('status', 'active')->get();

        return view('auth.firm-selection', compact('firms', 'financialYears'));
    }

    // ─────────────────────────────────────────────────────────────
    // Handle Step 2 Submission
    // ─────────────────────────────────────────────────────────────
    public function submitFirmSelection(Request $request)
    {
        if (session('login_type') !== 'firm' || !session('firm_temp_authenticated')) {
            return redirect()->route('login');
        }

        $request->validate([
            'firm_id'           => 'required|integer',
            'financial_year_id' => 'required|integer',
        ]);

        // Security: Validate selected firm matches their logged-in temp_firm_id
        if ((int)$request->firm_id !== (int)session('temp_firm_id')) {
            return back()->with('error', 'Unauthorized firm selection.');
        }

        // Security: Validate firm exists and is active
        $firm = Firm::where('id', $request->firm_id)->where('status', 'active')->first();
        if (!$firm) {
            return back()->with('error', 'The selected firm is inactive or does not exist.');
        }

        // Security: Validate financial year exists and is active
        $fy = \App\Models\FinancialYear::where('id', $request->financial_year_id)->where('status', 'active')->first();
        if (!$fy) {
            return back()->with('error', 'The selected Financial Year is inactive or does not exist.');
        }

        // Complete the authentication flow by finalizing session keys
        session()->forget('firm_temp_authenticated');
        session()->put([
            'firm_id'                 => $firm->id,
            'firm_name'               => $firm->firm_name,
            'financial_year_id'       => $fy->id,
            'financial_year_name'     => $fy->year_name,
            // Store User ID equivalent (the firm record ID itself)
            'user_id'                 => $firm->id,
        ]);

        AuditLog::log('Auth', 'Firm Login Complete', 'Firm ' . $firm->firm_name . ' selected Financial Year: ' . $fy->year_name);

        return $this->getIntendedOrDashboard();
    }

    // ─────────────────────────────────────────────────────────────
    // Helper: Safely resolve intended URL or fallback to dashboard
    // ─────────────────────────────────────────────────────────────
    private function getIntendedOrDashboard()
    {
        $intended = session('url.intended');
        if ($intended) {
            $path = parse_url($intended, PHP_URL_PATH);
            if (in_array($path, ['/login', '/firm-selection', '/logout', '/'])) {
                session()->forget('url.intended');
                return redirect()->route('dashboard');
            }
        }
        return redirect()->intended(route('dashboard'));
    }

    // ─────────────────────────────────────────────────────────────
    // Logout — handles both admin and firm
    // ─────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $loginType = session('login_type', 'admin');

        if ($loginType === 'firm') {
            AuditLog::log('Auth', 'Firm Logout', 'Firm logged out: ' . session('firm_name', ''));
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        }

        // Admin logout
        if (Auth::check()) {
            AuditLog::log('Auth', 'Logout', 'Admin logged out');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ─────────────────────────────────────────────────────────────
    // Show Change Password Form
    // ─────────────────────────────────────────────────────────────
    public function showChangePassword()
    {
        $loginType = session('login_type', 'admin');
        if ($loginType === 'firm') {
            $firm = Firm::find(session('firm_id'));
            $currentUser = (object)[
                'name'  => $firm->firm_name ?? session('firm_name', 'Firm Account'),
                'email' => $firm->email ?? session('firm_email', '-'),
                'role'  => 'Firm Account',
                'type'  => 'firm',
            ];
        } else {
            $user = Auth::user();
            $currentUser = (object)[
                'name'  => $user->name ?? 'Admin User',
                'email' => $user->email ?? '-',
                'role'  => is_object($user->role) ? ($user->role->name ?? $user->role->role_name ?? 'Administrator') : ucfirst($user->role ?? 'Administrator'),
                'type'  => 'admin',
            ];
        }

        return view('admin.profile.change-password', compact('currentUser'));
    }

    // ─────────────────────────────────────────────────────────────
    // Process Change Password Submission
    // ─────────────────────────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'current_password.required'      => 'Current password is required.',
            'password.required'              => 'New password is required.',
            'password.min'                   => 'New password must be at least 6 characters long.',
            'password.confirmed'             => 'New password and confirmation password do not match.',
            'password_confirmation.required' => 'Please confirm your new password.',
        ]);

        $loginType = session('login_type', 'admin');

        // Firm account password update
        if ($loginType === 'firm') {
            $firm = Firm::find(session('firm_id'));
            if (!$firm) {
                return back()->with('error', 'Firm session not found.');
            }

            if (!Hash::check($request->current_password, $firm->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match our records.'])->withInput();
            }

            if (Hash::check($request->password, $firm->password) || $request->current_password === $request->password) {
                return back()->withErrors(['password' => 'New password cannot be the same as your current password. Please choose a different password.'])->withInput();
            }

            $firm->password = Hash::make($request->password);
            $firm->save();

            AuditLog::log('Auth', 'Change Password', 'Firm password updated: ' . $firm->firm_name . ' (' . $firm->email . ')');

            return redirect()->route('change-password')->with('success', 'Password updated successfully!');
        }

        // Admin user password update
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match our records.'])->withInput();
        }

        if (Hash::check($request->password, $user->password) || $request->current_password === $request->password) {
            return back()->withErrors(['password' => 'New password cannot be the same as your current password. Please choose a different password.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        AuditLog::log('Auth', 'Change Password', 'Admin user password updated: ' . $user->name . ' (' . $user->email . ')');

        return redirect()->route('change-password')->with('success', 'Password updated successfully!');
    }
}