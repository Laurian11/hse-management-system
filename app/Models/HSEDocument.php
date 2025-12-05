<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class HSEDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'title', 'description', 'document_type',
        'category', 'document_code', 'department_id', 'created_by', 'approved_by',
        'approval_date', 'effective_date', 'review_date', 'expiry_date', 'status',
        'access_level', 'access_permissions', 'file_path', 'file_name', 'file_type',
        'file_size', 'retention_years', 'archival_date', 'notes',
    ];

    protected $casts = [
        'approval_date' => 'date',
        'effective_date' => 'date',
        'review_date' => 'date',
        'expiry_date' => 'date',
        'archival_date' => 'date',
        'access_permissions' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($document) {
            if (empty($document->reference_number)) {
                $document->reference_number = $document->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'hse_document_id');
    }

    public function currentVersion()
    {
        return $this->hasOne(DocumentVersion::class, 'hse_document_id')->where('is_current', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>', Carbon::now());
    }

    public function scopeRequiringReview($query)
    {
        return $query->whereNotNull('review_date')
            ->where('review_date', '<=', Carbon::now());
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'DOC-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
