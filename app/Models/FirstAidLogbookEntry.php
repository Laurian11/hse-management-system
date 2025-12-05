<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class FirstAidLogbookEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'incident_date', 'incident_time', 'injured_person_id',
        'injured_person_name', 'location', 'nature_of_injury', 'first_aid_provided', 'severity',
        'referred_to_medical', 'medical_facility', 'first_aider_id', 'treatment_details',
        'follow_up_required', 'follow_up_date', 'notes', 'attachments',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'incident_time' => 'datetime',
        'follow_up_date' => 'date',
        'referred_to_medical' => 'boolean',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($entry) {
            if (empty($entry->reference_number)) {
                $entry->reference_number = $entry->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function injuredPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'injured_person_id');
    }

    public function firstAider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_aider_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'FA-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
