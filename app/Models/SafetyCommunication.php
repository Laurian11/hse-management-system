<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafetyCommunication extends Model
{
    protected $fillable = [
        'reference_number',
        'company_id',
        'created_by',
        'communication_type',
        'priority_level',
        'title',
        'message',
        'attachments',
        'target_audience',
        'target_departments',
        'target_roles',
        'target_locations',
        'delivery_method',
        'delivery_channels',
        'requires_acknowledgment',
        'acknowledgment_deadline',
        'scheduled_send_time',
        'sent_at',
        'expires_at',
        'status',
        'total_recipients',
        'acknowledged_count',
        'read_count',
        'acknowledgment_rate',
        'regulatory_reference',
        'related_incidents',
        'quiz_questions',
        'is_multilingual',
        'translations',
        'language',
    ];

    protected $casts = [
        'attachments' => 'array',
        'target_departments' => 'array',
        'target_roles' => 'array',
        'target_locations' => 'array',
        'delivery_channels' => 'array',
        'acknowledgment_deadline' => 'datetime',
        'scheduled_send_time' => 'datetime',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'acknowledgment_rate' => 'decimal:2',
        'related_incidents' => 'array',
        'quiz_questions' => 'array',
        'translations' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'SC';
        $year = date('Y');
        $month = date('m');
        $sequence = $this->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count() + 1;
        
        return "{$prefix}-{$year}{$month}-{$sequence}";
    }

    public function calculateAcknowledgmentRate(): void
    {
        $this->acknowledgment_rate = $this->total_recipients > 0 
            ? ($this->acknowledged_count / $this->total_recipients) * 100 
            : 0;
        $this->save();
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('communication_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority_level', $priority);
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeRequiresAcknowledgment($query)
    {
        return $query->where('requires_acknowledgment', true);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now())
                    ->orWhereNull('expires_at');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']);
    }

    public function getTargetAudienceLabel(): string
    {
        $labels = [
            'all_employees' => 'All Employees',
            'specific_departments' => 'Specific Departments',
            'specific_roles' => 'Specific Roles',
            'specific_locations' => 'Specific Locations',
            'management_only' => 'Management Only',
            'supervisors_only' => 'Supervisors Only',
        ];

        return $labels[$this->target_audience] ?? $this->target_audience;
    }

    public function getPriorityColor(): string
    {
        $colors = [
            'low' => 'text-gray-600',
            'medium' => 'text-blue-600',
            'high' => 'text-orange-600',
            'critical' => 'text-red-600',
            'emergency' => 'text-red-800 bg-red-100',
        ];

        return $colors[$this->priority_level] ?? 'text-gray-600';
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'draft' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Draft</span>',
            'scheduled' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Scheduled</span>',
            'sent' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Sent</span>',
            'expired' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Expired</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '';
    }
}
