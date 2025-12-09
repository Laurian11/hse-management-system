<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiometricDevice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'device_name',
        'device_serial_number',
        'device_type',
        'company_id',
        'location_name',
        'location_address',
        'latitude',
        'longitude',
        'device_ip',
        'port',
        'api_key',
        'connection_type',
        'status',
        'last_sync_at',
        'last_connected_at',
        'sync_interval_minutes',
        'auto_sync_enabled',
        'daily_attendance_enabled',
        'toolbox_attendance_enabled',
        'work_start_time',
        'work_end_time',
        'grace_period_minutes',
        'notes',
        'configuration',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'port' => 'integer',
        'sync_interval_minutes' => 'integer',
        'grace_period_minutes' => 'integer',
        'auto_sync_enabled' => 'boolean',
        'daily_attendance_enabled' => 'boolean',
        'toolbox_attendance_enabled' => 'boolean',
        'work_start_time' => 'datetime:H:i',
        'work_end_time' => 'datetime:H:i',
        'last_sync_at' => 'datetime',
        'last_connected_at' => 'datetime',
        'configuration' => 'array',
    ];

    /**
     * Relationships
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function dailyAttendances(): HasMany
    {
        return $this->hasMany(DailyAttendance::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByLocation($query, $locationName)
    {
        return $query->where('location_name', $locationName);
    }

    public function scopeDailyAttendanceEnabled($query)
    {
        return $query->where('daily_attendance_enabled', true);
    }

    /**
     * Helper Methods
     */
    public function isOnline(): bool
    {
        if (!$this->last_connected_at) {
            return false;
        }
        
        // Consider device online if connected within last 5 minutes
        return $this->last_connected_at->diffInMinutes(now()) < 5;
    }

    public function needsSync(): bool
    {
        if (!$this->auto_sync_enabled) {
            return false;
        }
        
        if (!$this->last_sync_at) {
            return true;
        }
        
        return $this->last_sync_at->diffInMinutes(now()) >= $this->sync_interval_minutes;
    }

    public function getConnectionUrl(): string
    {
        return "http://{$this->device_ip}:{$this->port}";
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'active' => $this->isOnline() ? 'green' : 'yellow',
            'inactive' => 'gray',
            'maintenance' => 'orange',
            'offline' => 'red',
            default => 'gray',
        };
    }

    public function getStatusBadge(): string
    {
        $color = $this->getStatusColor();
        $status = ucfirst($this->status);
        
        if ($this->status === 'active' && !$this->isOnline()) {
            $status = 'Active (Offline)';
            $color = 'yellow';
        }
        
        return "<span class='px-2 py-1 text-xs rounded-full bg-{$color}-100 text-{$color}-800'>{$status}</span>";
    }
}
