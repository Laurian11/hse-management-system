@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('toolbox-talks.show', $toolboxTalk) }}" class="text-blue-600 hover:text-blue-700 mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Talk
        </a>
        <h1 class="text-2xl font-bold text-primary-black">Submit Feedback</h1>
        <p class="text-medium-gray mt-2">Share your thoughts about: <strong>{{ $toolboxTalk->title }}</strong></p>
    </div>

    <form action="{{ route('toolbox-talks.submit-feedback', $toolboxTalk) }}" method="POST" class="bg-white border border-border-gray p-6 rounded">
        @csrf

        <!-- Feedback Type -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-primary-black mb-2">Feedback Type</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <label class="flex items-center p-3 border border-border-gray rounded cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="feedback_type" value="quick_rating" checked class="mr-2" onchange="toggleFeedbackType()">
                    <span class="text-sm">Quick Rating</span>
                </label>
                <label class="flex items-center p-3 border border-border-gray rounded cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="feedback_type" value="detailed_survey" class="mr-2" onchange="toggleFeedbackType()">
                    <span class="text-sm">Detailed Survey</span>
                </label>
                <label class="flex items-center p-3 border border-border-gray rounded cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="feedback_type" value="suggestion" class="mr-2" onchange="toggleFeedbackType()">
                    <span class="text-sm">Suggestion</span>
                </label>
                <label class="flex items-center p-3 border border-border-gray rounded cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="feedback_type" value="complaint" class="mr-2" onchange="toggleFeedbackType()">
                    <span class="text-sm">Complaint</span>
                </label>
            </div>
        </div>

        <!-- Quick Rating Section -->
        <div id="quickRatingSection">
            <div class="mb-6">
                <label class="block text-sm font-medium text-primary-black mb-2">Overall Rating</label>
                <div class="flex items-center gap-2" id="ratingStars">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" onclick="setRating({{ $i }})" class="text-3xl text-gray-300 hover:text-yellow-400 rating-star" data-rating="{{ $i }}">
                            <i class="fas fa-star"></i>
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="overall_rating" id="overall_rating" value="">
            </div>
        </div>

        <!-- Detailed Survey Section -->
        <div id="detailedSurveySection" class="hidden">
            <h3 class="text-lg font-semibold text-primary-black mb-4">Detailed Ratings</h3>
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Presenter Effectiveness</label>
                    <select name="presenter_effectiveness" class="w-full border border-border-gray rounded px-3 py-2">
                        <option value="">Select rating</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }} - {{ $i == 1 ? 'Poor' : ($i == 5 ? 'Excellent' : 'Good') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Topic Relevance</label>
                    <select name="topic_relevance" class="w-full border border-border-gray rounded px-3 py-2">
                        <option value="">Select rating</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }} - {{ $i == 1 ? 'Not Relevant' : ($i == 5 ? 'Very Relevant' : 'Relevant') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Content Clarity</label>
                    <select name="content_clarity" class="w-full border border-border-gray rounded px-3 py-2">
                        <option value="">Select rating</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }} - {{ $i == 1 ? 'Unclear' : ($i == 5 ? 'Very Clear' : 'Clear') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary-black mb-2">Engagement Level</label>
                    <select name="engagement_level" class="w-full border border-border-gray rounded px-3 py-2">
                        <option value="">Select rating</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }} - {{ $i == 1 ? 'Low' : ($i == 5 ? 'Very High' : 'Moderate') }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-primary-black mb-2">Most Valuable Point</label>
            <textarea name="most_valuable_point" rows="3" class="w-full border border-border-gray rounded px-3 py-2" 
                      placeholder="What was the most valuable thing you learned?"></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-primary-black mb-2">Improvement Suggestions</label>
            <textarea name="improvement_suggestion" rows="3" class="w-full border border-border-gray rounded px-3 py-2" 
                      placeholder="How can we improve this talk?"></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-primary-black mb-2">Additional Comments</label>
            <textarea name="specific_comments" rows="4" class="w-full border border-border-gray rounded px-3 py-2" 
                      placeholder="Any other comments or feedback?"></textarea>
        </div>

        <!-- Additional Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-primary-black mb-2">Format Preference</label>
                <select name="format_preference" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">No Preference</option>
                    <option value="presentation_only">Presentation Only</option>
                    <option value="discussion_heavy">Discussion Heavy</option>
                    <option value="hands_on">Hands On</option>
                    <option value="video_based">Video Based</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-2">Would You Recommend?</label>
                <div class="flex gap-4 mt-2">
                    <label class="flex items-center">
                        <input type="radio" name="would_recommend" value="1" class="mr-2">
                        <span>Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="would_recommend" value="0" class="mr-2">
                        <span>No</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-primary-black mb-2">Topic Requests</label>
            <textarea name="additional_topics" rows="2" class="w-full border border-border-gray rounded px-3 py-2" 
                      placeholder="What topics would you like to see in future talks?"></textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane mr-2"></i>Submit Feedback
            </button>
            <a href="{{ route('toolbox-talks.show', $toolboxTalk) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
let currentRating = 0;

function setRating(rating) {
    currentRating = rating;
    document.getElementById('overall_rating').value = rating;
    
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

function toggleFeedbackType() {
    const type = document.querySelector('input[name="feedback_type"]:checked').value;
    const quickSection = document.getElementById('quickRatingSection');
    const detailedSection = document.getElementById('detailedSurveySection');
    
    if (type === 'detailed_survey') {
        quickSection.classList.add('hidden');
        detailedSection.classList.remove('hidden');
    } else {
        quickSection.classList.remove('hidden');
        detailedSection.classList.add('hidden');
    }
}
</script>
@endsection

