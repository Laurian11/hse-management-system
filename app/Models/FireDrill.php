<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class FireDrill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'drill_date', 'drill_time', 'location',
        'drill_type', 'objectives', 'scenario', 'total_participants', 'expected_participants',
        'participants', 'evacuation_time', 'overall_result', 'observations',
        'strengths', 'weaknesses', 'recommendations', 'conducted_by', 'observers',
        'requires_follow_up', 'follow_up_actions', 'follow_up_due_date',
        'follow_up_completed', 'attachments',
    ];

    protected $casts = [
        'drill_date' => 'date',
        'participants' => 'array',
        'observers' => 'array',
        'strengths' => 'array',
        'weaknesses' => 'array',
        'recommendations' => 'array',
        'attachments' => 'array',
        'follow_up_due_date' => 'date',
        'requires_follow_up' => 'boolean',
        'follow_up_completed' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($drill) {
            if (empty($drill->reference_number)) {
                $drill->reference_number = $drill->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function conductedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('requires_follow_up', true)
                     ->where('follow_up_completed', false);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'FD-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
