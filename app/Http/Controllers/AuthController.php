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
        $user = User::where('email', $request->email)->first();

        if ($user && $user->status !== 'active') {
            return back()
                ->withInput($request->only('email', 'login_type'))
                ->with('error', 'Your account is inactive. Please contact admin.');
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->put('login_type', 'admin');
            AuditLog::log('Auth', 'Login', 'Admin logged in: ' . $request->email);
            return redirect()->intended(route('dashboard'));
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
        // Find firm by email
        $firm = Firm::where('email', $request->email)->first();

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
        if (! Hash::check($request->password, $firm->password)) {
            return back()
                ->withInput($request->only('email', 'login_type'))
                ->with('error', 'Invalid Login ID or Password.');
        }

        // ✅ Authenticated — store temporary authenticated firm session
        $request->session()->regenerate();
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
}