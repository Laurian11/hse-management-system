<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'description', 'trigger_type', 'notification_channel',
        'recipients', 'conditions', 'days_before', 'days_after', 'frequency',
        'notification_time', 'message_template', 'is_active', 'created_by',
    ];

    protected $casts = [
        'recipients' => 'array',
        'conditions' => 'array',
        'is_active' => 'boolean',
        'notification_time' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTriggerType($query, $type)
    {
        return $query->where('trigger_type', $type);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('notification_channel', $channel);
    }
}
