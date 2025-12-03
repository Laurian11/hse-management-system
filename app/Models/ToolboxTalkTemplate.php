<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToolboxTalkTemplate extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'title',
        'description_content',
        'topic_id',
        'talk_type',
        'duration_minutes',
        'key_points',
        'regulatory_references',
        'materials',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'materials' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ToolboxTalkTopic::class);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

