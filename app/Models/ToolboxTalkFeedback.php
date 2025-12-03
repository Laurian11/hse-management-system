<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolboxTalkFeedback extends Model
{
    protected $fillable = [
        'toolbox_talk_id',
        'employee_id',
        'employee_name',
        'feedback_type',
        'overall_rating',
        'sentiment',
        'most_valuable_point',
        'improvement_suggestion',
        'presenter_effectiveness',
        'topic_relevance',
        'content_clarity',
        'engagement_level',
        'timing_appropriateness',
        'material_quality',
        'specific_comments',
        'topic_requests',
        'format_preference',
        'location_feedback',
        'time_preference',
        'would_recommend',
        'additional_topics',
        'response_method',
        'ip_address',
    ];

    protected $casts = [
        'topic_requests' => 'array',
        'overall_rating' => 'integer',
        'presenter_effectiveness' => 'integer',
        'topic_relevance' => 'integer',
        'content_clarity' => 'integer',
        'engagement_level' => 'integer',
        'timing_appropriateness' => 'integer',
        'material_quality' => 'integer',
        'would_recommend' => 'boolean',
    ];

    public function toolboxTalk(): BelongsTo
    {
        return $this->belongsTo(ToolboxTalk::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function scopeQuickRating($query)
    {
        return $query->where('feedback_type', 'quick_rating');
    }

    public function scopeDetailedSurvey($query)
    {
        return $query->where('feedback_type', 'detailed_survey');
    }

    public function scopeSuggestion($query)
    {
        return $query->where('feedback_type', 'suggestion');
    }

    public function scopeComplaint($query)
    {
        return $query->where('feedback_type', 'complaint');
    }

    public function scopePositive($query)
    {
        return $query->where('sentiment', 'positive');
    }

    public function scopeNeutral($query)
    {
        return $query->where('sentiment', 'neutral');
    }

    public function scopeNegative($query)
    {
        return $query->where('sentiment', 'negative');
    }

    public function scopeWithRating($query)
    {
        return $query->whereNotNull('overall_rating');
    }

    public function getOverallRatingStars(): string
    {
        if (!$this->overall_rating) {
            return 'Not Rated';
        }

        $stars = str_repeat('⭐', $this->overall_rating) . str_repeat('☆', 5 - $this->overall_rating);
        return $stars . " ({$this->overall_rating}/5)";
    }

    public function getSentimentBadge(): string
    {
        $badges = [
            'positive' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Positive</span>',
            'neutral' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Neutral</span>',
            'negative' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Negative</span>',
        ];

        return $badges[$this->sentiment] ?? '';
    }

    public function getFeedbackTypeBadge(): string
    {
        $badges = [
            'quick_rating' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Quick Rating</span>',
            'detailed_survey' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Detailed Survey</span>',
            'suggestion' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Suggestion</span>',
            'complaint' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Complaint</span>',
        ];

        return $badges[$this->feedback_type] ?? '';
    }

    public function getAverageDetailedRating(): ?float
    {
        $ratings = [
            $this->presenter_effectiveness,
            $this->topic_relevance,
            $this->content_clarity,
            $this->engagement_level,
            $this->timing_appropriateness,
            $this->material_quality,
        ];

        $validRatings = array_filter($ratings, fn($rating) => $rating !== null);

        return count($validRatings) > 0 ? round(array_sum($validRatings) / count($validRatings), 2) : null;
    }

    public function hasActionableFeedback(): bool
    {
        return !empty($this->improvement_suggestion) || 
               !empty($this->specific_comments) ||
               $this->sentiment === 'negative' ||
               ($this->overall_rating && $this->overall_rating <= 2);
    }

    public function isPositiveSentiment(): bool
    {
        return $this->sentiment === 'positive' || 
               ($this->overall_rating && $this->overall_rating >= 4);
    }

    public function isNegativeSentiment(): bool
    {
        return $this->sentiment === 'negative' || 
               ($this->overall_rating && $this->overall_rating <= 2);
    }

    public function getResponseMethodLabel(): string
    {
        $labels = [
            'mobile_app' => 'Mobile App',
            'paper_form' => 'Paper Form',
            'email_survey' => 'Email Survey',
            'tablet_kiosk' => 'Tablet Kiosk',
        ];

        return $labels[$this->response_method] ?? $this->response_method;
    }

    public function getFormatPreferenceLabel(): string
    {
        $labels = [
            'presentation_only' => 'Presentation Only',
            'discussion_heavy' => 'Discussion Heavy',
            'hands_on' => 'Hands On',
            'video_based' => 'Video Based',
        ];

        return $labels[$this->format_preference] ?? 'No Preference';
    }

    public function calculateEngagementScore(): int
    {
        $score = 0;

        // Rating contributes to engagement
        if ($this->overall_rating) {
            $score += $this->overall_rating;
        }

        // Detailed ratings contribute more
        if ($this->presenter_effectiveness) $score += 1;
        if ($this->topic_relevance) $score += 1;
        if ($this->content_clarity) $score += 1;

        // Comments show engagement
        if (!empty($this->specific_comments)) $score += 1;
        if (!empty($this->improvement_suggestion)) $score += 1;
        if (!empty($this->most_valuable_point)) $score += 1;

        // Topic requests show engagement
        if (!empty($this->topic_requests)) $score += 1;
        if (!empty($this->additional_topics)) $score += 1;

        return min(10, $score); // Cap at 10
    }

    public function getFeedbackSummary(): array
    {
        return [
            'rating' => $this->overall_rating,
            'sentiment' => $this->sentiment,
            'has_suggestions' => !empty($this->improvement_suggestion),
            'has_comments' => !empty($this->specific_comments),
            'would_recommend' => $this->would_recommend,
            'engagement_score' => $this->calculateEngagementScore(),
            'is_actionable' => $this->hasActionableFeedback(),
        ];
    }

    public static function getFeedbackAnalytics($toolboxTalkId): array
    {
        $feedback = self::where('toolbox_talk_id', $toolboxTalkId)->get();

        return [
            'total_responses' => $feedback->count(),
            'average_rating' => $feedback->withRating()->avg('overall_rating'),
            'sentiment_breakdown' => [
                'positive' => $feedback->positive()->count(),
                'neutral' => $feedback->neutral()->count(),
                'negative' => $feedback->negative()->count(),
            ],
            'type_breakdown' => [
                'quick_rating' => $feedback->quickRating()->count(),
                'detailed_survey' => $feedback->detailedSurvey()->count(),
                'suggestion' => $feedback->suggestion()->count(),
                'complaint' => $feedback->complaint()->count(),
            ],
            'recommendation_rate' => $feedback->where('would_recommend', true)->count() / max(1, $feedback->count()) * 100,
            'actionable_feedback_count' => $feedback->filter(fn($f) => $f->hasActionableFeedback())->count(),
        ];
    }
}
