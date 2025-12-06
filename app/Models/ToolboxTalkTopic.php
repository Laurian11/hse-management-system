<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToolboxTalkTopic extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'subcategory',
        'difficulty_level',
        'estimated_duration_minutes',
        'presentation_content',
        'discussion_questions',
        'quiz_questions',
        'required_materials',
        'learning_objectives',
        'key_talking_points',
        'real_world_examples',
        'related_incidents',
        'regulatory_references',
        'department_relevance',
        'seasonal_relevance',
        'is_active',
        'is_mandatory',
        'usage_count',
        'average_feedback_score',
        'effectiveness_rating',
        'created_by',
        'representer_id',
    ];

    protected $casts = [
        'presentation_content' => 'array',
        'discussion_questions' => 'array',
        'quiz_questions' => 'array',
        'required_materials' => 'array',
        'learning_objectives' => 'array',
        'related_incidents' => 'array',
        'department_relevance' => 'array',
        'average_feedback_score' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function representer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representer_id');
    }

    public function toolboxTalks(): HasMany
    {
        return $this->hasMany(ToolboxTalk::class, 'topic_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeBySeasonalRelevance($query, $season)
    {
        return $query->where('seasonal_relevance', $season)
                    ->orWhere('seasonal_relevance', 'all_year');
    }

    public function scopeMostUsed($query, $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')
                    ->limit($limit);
    }

    public function scopeHighestRated($query, $limit = 10)
    {
        return $query->whereNotNull('average_feedback_score')
                    ->orderBy('average_feedback_score', 'desc')
                    ->limit($limit);
    }

    public function incrementUsageCount(): void
    {
        $this->increment('usage_count');
    }

    public function updateFeedbackScore(): void
    {
        $averageScore = $this->toolboxTalks()
            ->whereNotNull('average_feedback_score')
            ->avg('average_feedback_score');
        
        $this->average_feedback_score = $averageScore ? round($averageScore, 2) : null;
        $this->save();
    }

    public function isRelevantToDepartment($departmentId): bool
    {
        if (!$this->department_relevance) {
            return true; // If no specific departments, relevant to all
        }

        return in_array($departmentId, $this->department_relevance);
    }

    public function getSeasonalTopics(): array
    {
        $currentMonth = date('n');
        $season = $this->getSeasonFromMonth($currentMonth);
        
        return self::where('seasonal_relevance', $season)
                  ->orWhere('seasonal_relevance', 'all_year')
                  ->active()
                  ->get()
                  ->toArray();
    }

    private function getSeasonFromMonth(int $month): string
    {
        if (in_array($month, [12, 1, 2])) {
            return 'winter';
        } elseif (in_array($month, [3, 4, 5])) {
            return 'summer';
        } elseif (in_array($month, [6, 7, 8])) {
            return 'monsoon';
        } else {
            return 'extreme_heat';
        }
    }
}
