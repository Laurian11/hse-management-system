<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentVersion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hse_document_id', 'version_number', 'change_summary', 'created_by',
        'reviewed_by', 'approved_by', 'review_date', 'approval_date', 'status',
        'file_path', 'file_name', 'is_current', 'rejection_reason',
    ];

    protected $casts = [
        'review_date' => 'date',
        'approval_date' => 'date',
        'is_current' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($version) {
            // When a new version is marked as current, unmark all other versions
            if ($version->is_current) {
                static::where('hse_document_id', $version->hse_document_id)
                    ->update(['is_current' => false]);
            }
        });

        static::updating(function ($version) {
            // When updating to current, unmark all other versions
            if ($version->is_current && $version->isDirty('is_current')) {
                static::where('hse_document_id', $version->hse_document_id)
                    ->where('id', '!=', $version->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(HSEDocument::class, 'hse_document_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeForDocument($query, $documentId)
    {
        return $query->where('hse_document_id', $documentId);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
