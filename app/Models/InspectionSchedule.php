<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class InspectionSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'title', 'description', 'frequency',
        'custom_days', 'start_date', 'end_date', 'next_inspection_date',
        'assigned_to', 'department_id', 'inspection_checklist_id', 'is_active', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_inspection_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($schedule) {
            if (empty($schedule->reference_number)) {
                $schedule->reference_number = $schedule->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(InspectionChecklist::class, 'inspection_checklist_id');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('next_inspection_date', '<=', Carbon::now()->toDateString())
                     ->where('is_active', true);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'INS-SCH-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    public function calculateNextInspectionDate(): ?Carbon
    {
        if (!$this->next_inspection_date) {
            return null;
        }

        $nextDate = Carbon::parse($this->next_inspection_date);

        switch ($this->frequency) {
            case 'daily':
                return $nextDate->addDay();
            case 'weekly':
                return $nextDate->addWeek();
            case 'monthly':
                return $nextDate->addMonth();
            case 'quarterly':
                return $nextDate->addMonths(3);
            case 'annually':
                return $nextDate->addYear();
            case 'custom':
                return $nextDate->addDays($this->custom_days ?? 30);
            default:
                return $nextDate->addMonth();
        }
    }
}
