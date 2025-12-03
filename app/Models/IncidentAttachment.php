<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class IncidentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'incident_id',
        'company_id',
        'uploaded_by',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_extension',
        'file_size',
        'category',
        'description',
        'tags',
        'metadata',
        'is_evidence',
        'is_confidential',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'metadata' => 'array',
        'is_evidence' => 'boolean',
        'is_confidential' => 'boolean',
    ];

    // Relationships
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePhotos($query)
    {
        return $query->where('category', 'photo');
    }

    public function scopeVideos($query)
    {
        return $query->where('category', 'video');
    }

    public function scopeDocuments($query)
    {
        return $query->where('category', 'document');
    }

    public function scopeEvidence($query)
    {
        return $query->where('is_evidence', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Helper Methods
    public function getFileUrl(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeHuman(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return in_array($this->file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function isVideo(): bool
    {
        return in_array($this->file_extension, ['mp4', 'avi', 'mov', 'wmv', 'flv']);
    }

    public function isDocument(): bool
    {
        return in_array($this->file_extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']);
    }

    public function getCategoryBadge(): string
    {
        $badges = [
            'photo' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Photo</span>',
            'video' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Video</span>',
            'document' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Document</span>',
            'witness_statement' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Witness Statement</span>',
            'interview_recording' => '<span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">Interview</span>',
            'other' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Other</span>',
        ];

        return $badges[$this->category] ?? $badges['other'];
    }

    /**
     * Delete the file when the model is deleted
     */
    protected static function booted()
    {
        static::deleted(function ($attachment) {
            if ($attachment->isForceDeleting()) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        });
    }
}
