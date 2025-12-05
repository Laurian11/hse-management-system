<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class HealthSurveillanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'user_id', 'surveillance_type', 'examination_name',
        'examination_date', 'next_due_date', 'medical_provider_id', 'provider_name', 'findings',
        'result', 'restrictions', 'recommendations', 'conducted_by', 'notes', 'attachments',
    ];

    protected $casts = [
        'examination_date' => 'date',
        'next_due_date' => 'date',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($record) {
            if (empty($record->reference_number)) {
                $record->reference_number = $record->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalProvider(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'medical_provider_id');
    }

    public function conductedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeDue($query)
    {
        return $query->where('next_due_date', '<=', Carbon::now()->toDateString());
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'HSR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
