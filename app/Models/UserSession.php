<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'last_activity_at',
        'is_active',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('login_at', '>=', now()->subHours($hours));
    }

    public function scopeLongRunning($query, $hours = 8)
    {
        return $query->active()
                    ->where('login_at', '<=', now()->subHours($hours));
    }

    public function scopeStale($query, $hours = 2)
    {
        return $query->active()
                    ->where('last_activity_at', '<=', now()->subHours($hours));
    }

    // Helper Methods
    public function getDuration(): string
    {
        if (!$this->login_at) {
            return 'N/A';
        }

        $end = $this->logout_at ?: now();
        $duration = $this->login_at->diff($end);

        if ($duration->days > 0) {
            return $duration->format('%d days %H:%I:%S');
        }

        return $duration->format('%H:%I:%S');
    }

    public function getDurationInMinutes(): int
    {
        if (!$this->login_at) {
            return 0;
        }

        $end = $this->logout_at ?: now();
        return $this->login_at->diffInMinutes($end);
    }

    public function getDurationInHours(): float
    {
        return $this->getDurationInMinutes() / 60;
    }

    public function isLongRunning(): bool
    {
        return $this->getDurationInHours() >= 8;
    }

    public function isStale(): bool
    {
        return $this->is_active && 
               $this->last_activity_at && 
               $this->last_activity_at->lt(now()->subHours(2));
    }

    public function getBrowserInfo(): array
    {
        $userAgent = $this->user_agent;
        
        $browser = 'Unknown';
        $os = 'Unknown';
        $device = 'Desktop';

        if (preg_match('/Chrome\/[\d.]+/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox\/[\d.]+/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari\/[\d.]+/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge\/[\d.]+/', $userAgent)) {
            $browser = 'Edge';
        }

        if (preg_match('/Windows/', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $os = 'Android';
            $device = 'Mobile';
        } elseif (preg_match('/iPhone|iPad/', $userAgent)) {
            $os = 'iOS';
            $device = 'Mobile';
        }

        return [
            'browser' => $browser,
            'os' => $os,
            'device' => $device,
        ];
    }

    public function getBrowserIcon(): string
    {
        $info = $this->getBrowserInfo();
        
        $icons = [
            'Chrome' => 'fab fa-chrome',
            'Firefox' => 'fab fa-firefox',
            'Safari' => 'fab fa-safari',
            'Edge' => 'fab fa-edge',
        ];

        return $icons[$info['browser']] ?? 'fas fa-globe';
    }

    public function getOSIcon(): string
    {
        $info = $this->getBrowserInfo();
        
        $icons = [
            'Windows' => 'fab fa-windows',
            'macOS' => 'fab fa-apple',
            'Linux' => 'fab fa-linux',
            'Android' => 'fab fa-android',
            'iOS' => 'fab fa-apple',
        ];

        return $icons[$info['os']] ?? 'fas fa-desktop';
    }

    public function getDeviceIcon(): string
    {
        $info = $this->getBrowserInfo();
        
        return $info['device'] === 'Mobile' ? 'fas fa-mobile-alt' : 'fas fa-desktop';
    }

    public function getLocation(): string
    {
        // This could be enhanced with IP geolocation
        return $this->ip_address;
    }

    public function getStatusBadge(): string
    {
        if (!$this->is_active) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Logged Out</span>';
        }

        if ($this->isStale()) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Stale</span>';
        }

        if ($this->isLongRunning()) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Long Running</span>';
        }

        return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>';
    }

    public function endSession()
    {
        $this->update([
            'logout_at' => now(),
            'is_active' => false,
        ]);
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    // Static Methods
    public static function startSession($user, $sessionId, $ipAddress, $userAgent)
    {
        return self::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_at' => now(),
            'last_activity_at' => now(),
            'is_active' => true,
        ]);
    }

    public static function endSessionBySessionId($sessionId)
    {
        $session = self::where('session_id', $sessionId)->active()->first();
        if ($session) {
            $session->endSession();
        }
    }

    public static function endAllSessionsForUser($userId)
    {
        self::where('user_id', $userId)->active()->get()->each(function ($session) {
            $session->endSession();
        });
    }

    public static function getActiveSessions($companyId = null)
    {
        $query = self::with('user')
                     ->active()
                     ->latest('login_at');

        if ($companyId) {
            $query->whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        return $query->get();
    }

    public static function getStaleSessions($hours = 2, $companyId = null)
    {
        $query = self::with('user')
                     ->stale($hours);

        if ($companyId) {
            $query->whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        return $query->get();
    }

    public static function getLongRunningSessions($hours = 8, $companyId = null)
    {
        $query = self::with('user')
                     ->longRunning($hours);

        if ($companyId) {
            $query->whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        return $query->get();
    }

    public static function getSessionStatistics($companyId = null)
    {
        $query = self::query();

        if ($companyId) {
            $query->whereHas('user', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $total = $query->count();
        $active = $query->active()->count();
        $stale = $query->stale()->count();
        $longRunning = $query->longRunning()->count();

        return [
            'total_sessions' => $total,
            'active_sessions' => $active,
            'stale_sessions' => $stale,
            'long_running_sessions' => $longRunning,
            'stale_percentage' => $total > 0 ? round(($stale / $total) * 100, 2) : 0,
            'long_running_percentage' => $total > 0 ? round(($longRunning / $total) * 100, 2) : 0,
        ];
    }

    public static function cleanupStaleSessions($hours = 24)
    {
        $staleSessions = self::stale($hours)->get();
        
        $staleSessions->each(function ($session) {
            $session->endSession();
        });

        return $staleSessions->count();
    }
}
