<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class TrainingMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'title',
        'description',
        'material_type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'external_url',
        'content',
        'version',
        'created_by',
        'updated_by',
        'tags',
        'applicable_training_types',
        'status',
        'is_standard',
        'is_public',
        'reviewed_by',
        'reviewed_at',
        'next_review_date',
    ];

    protected $casts = [
        'tags' => 'array',
        'applicable_training_types' => 'array',
        'is_standard' => 'boolean',
        'is_public' => 'boolean',
        'reviewed_at' => 'datetime',
        'next_review_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($material) {
            if (empty($material->reference_number)) {
                $material->reference_number = 'TM-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($material) {
            ActivityLog::log('create', 'training', 'TrainingMaterial', $material->id, "Created training material: {$material->title}");
        });
    }

    // Relationships
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('material_type', $type);
    }

    public function scopeStandard($query)
    {
        return $query->where('is_standard', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Helper Methods
    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }
}
