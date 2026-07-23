<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'module_name',
        'action_type',
        'description',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an activity.
     */
    public static function log($module, $action, $description)
    {
        // Prefer Laravel-authenticated admin user
        $user = Auth::user();

        if ($user) {
            $userId   = $user->id;
            $userName = $user->name;
        } elseif (session('login_type') === 'firm' && session('firm_id')) {
            // Firm session — no Auth user but we have session data
            $userId   = null;
            $userName = session('firm_name', 'Firm') . ' (Firm #' . session('firm_id') . ')';
        } else {
            $userId   = null;
            $userName = 'System/Guest';
        }

        self::create([
            'user_id'     => $userId,
            'user_name'   => $userName,
            'module_name' => $module,
            'action_type' => $action,
            'description' => $description,
            'ip_address'  => Request::ip() ?? '127.0.0.1',
        ]);
    }
}
