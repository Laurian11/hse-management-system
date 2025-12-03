@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-black">Feedback - {{ $toolboxTalk->title }}</h1>
        <div class="flex gap-3">
            <a href="{{ route('toolbox-talks.submit-feedback', $toolboxTalk) }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Submit Feedback
            </a>
            <a href="{{ route('toolbox-talks.show', $toolboxTalk) }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Talk
            </a>
        </div>
    </div>

    <!-- Analytics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Total Responses</div>
            <div class="text-2xl font-bold text-primary-black">{{ $analytics['total_responses'] }}</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Average Rating</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($analytics['average_rating'] ?? 0, 1) }}/5</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Recommendation Rate</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($analytics['recommendation_rate'] ?? 0, 1) }}%</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Actionable Feedback</div>
            <div class="text-2xl font-bold text-purple-600">{{ $analytics['actionable_feedback_count'] }}</div>
        </div>
    </div>

    <!-- Sentiment Breakdown -->
    <div class="bg-white border border-border-gray p-6 rounded mb-6">
        <h2 class="text-lg font-semibold text-primary-black mb-4">Sentiment Breakdown</h2>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-4 bg-green-50 rounded">
                <div class="text-2xl font-bold text-green-600">{{ $analytics['sentiment_breakdown']['positive'] }}</div>
                <div class="text-sm text-medium-gray">Positive</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded">
                <div class="text-2xl font-bold text-gray-600">{{ $analytics['sentiment_breakdown']['neutral'] }}</div>
                <div class="text-sm text-medium-gray">Neutral</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded">
                <div class="text-2xl font-bold text-red-600">{{ $analytics['sentiment_breakdown']['negative'] }}</div>
                <div class="text-sm text-medium-gray">Negative</div>
            </div>
        </div>
    </div>

    <!-- Feedback List -->
    <div class="bg-white border border-border-gray rounded overflow-hidden">
        <div class="p-4 border-b border-border-gray">
            <h2 class="text-lg font-semibold text-primary-black">All Feedback Responses</h2>
        </div>
        <div class="divide-y divide-border-gray">
            @forelse($feedback as $item)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="font-medium text-primary-black">{{ $item->employee_name ?? 'Anonymous' }}</div>
                            <div class="text-sm text-medium-gray">{{ $item->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="flex gap-2">
                            {!! $item->getFeedbackTypeBadge() !!}
                            {!! $item->getSentimentBadge() !!}
                        </div>
                    </div>

                    @if($item->overall_rating)
                        <div class="mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-medium-gray">Rating:</span>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $item->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="text-sm text-medium-gray">({{ $item->overall_rating }}/5)</span>
                            </div>
                        </div>
                    @endif

                    @if($item->most_valuable_point)
                        <div class="mb-3">
                            <div class="text-sm font-medium text-primary-black mb-1">Most Valuable Point:</div>
                            <div class="text-sm text-medium-gray">{{ $item->most_valuable_point }}</div>
                        </div>
                    @endif

                    @if($item->improvement_suggestion)
                        <div class="mb-3">
                            <div class="text-sm font-medium text-primary-black mb-1">Improvement Suggestion:</div>
                            <div class="text-sm text-medium-gray">{{ $item->improvement_suggestion }}</div>
                        </div>
                    @endif

                    @if($item->specific_comments)
                        <div class="mb-3">
                            <div class="text-sm font-medium text-primary-black mb-1">Comments:</div>
                            <div class="text-sm text-medium-gray">{{ $item->specific_comments }}</div>
                        </div>
                    @endif

                    @if($item->additional_topics)
                        <div class="mb-3">
                            <div class="text-sm font-medium text-primary-black mb-1">Topic Requests:</div>
                            <div class="text-sm text-medium-gray">{{ $item->additional_topics }}</div>
                        </div>
                    @endif

                    @if($item->presenter_effectiveness || $item->topic_relevance || $item->content_clarity)
                        <div class="mt-3 pt-3 border-t border-border-gray">
                            <div class="text-sm font-medium text-primary-black mb-2">Detailed Ratings:</div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                                @if($item->presenter_effectiveness)
                                    <div>Presenter: {{ $item->presenter_effectiveness }}/5</div>
                                @endif
                                @if($item->topic_relevance)
                                    <div>Relevance: {{ $item->topic_relevance }}/5</div>
                                @endif
                                @if($item->content_clarity)
                                    <div>Clarity: {{ $item->content_clarity }}/5</div>
                                @endif
                                @if($item->engagement_level)
                                    <div>Engagement: {{ $item->engagement_level }}/5</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-medium-gray">
                    <i class="fas fa-comment-slash text-4xl mb-4"></i>
                    <p>No feedback submitted yet</p>
                    <a href="{{ route('toolbox-talks.submit-feedback', $toolboxTalk) }}" class="btn-primary mt-4 inline-block">
                        Be the first to submit feedback
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

