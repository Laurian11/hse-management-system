@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-black">Feedback Collection</h1>
        <a href="{{ route('toolbox-talks.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Talks
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Total Feedback</div>
            <div class="text-2xl font-bold text-primary-black">{{ number_format($stats['total_feedback']) }}</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Average Rating</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['average_rating'], 1) }}/5</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">This Month</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['this_month']) }}</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Sentiment</div>
            <div class="flex gap-2 text-sm">
                <span class="text-green-600">+{{ $stats['positive'] }}</span>
                <span class="text-gray-600">~{{ $stats['neutral'] }}</span>
                <span class="text-red-600">-{{ $stats['negative'] }}</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-border-gray p-4 mb-6 rounded">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Talk</label>
                <select name="talk_id" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">All Talks</option>
                    @foreach($talks ?? [] as $talk)
                        <option value="{{ $talk->id }}" {{ request('talk_id') == $talk->id ? 'selected' : '' }}>
                            {{ $talk->title }} ({{ $talk->reference_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Sentiment</label>
                <select name="sentiment" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">All Sentiments</option>
                    <option value="positive" {{ request('sentiment') == 'positive' ? 'selected' : '' }}>Positive</option>
                    <option value="neutral" {{ request('sentiment') == 'neutral' ? 'selected' : '' }}>Neutral</option>
                    <option value="negative" {{ request('sentiment') == 'negative' ? 'selected' : '' }}>Negative</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Type</label>
                <select name="feedback_type" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">All Types</option>
                    <option value="quick_rating" {{ request('feedback_type') == 'quick_rating' ? 'selected' : '' }}>Quick Rating</option>
                    <option value="detailed_survey" {{ request('feedback_type') == 'detailed_survey' ? 'selected' : '' }}>Detailed Survey</option>
                    <option value="suggestion" {{ request('feedback_type') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                    <option value="complaint" {{ request('feedback_type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Min Rating</label>
                <select name="rating_min" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">Any Rating</option>
                    <option value="5" {{ request('rating_min') == '5' ? 'selected' : '' }}>5 Stars</option>
                    <option value="4" {{ request('rating_min') == '4' ? 'selected' : '' }}>4+ Stars</option>
                    <option value="3" {{ request('rating_min') == '3' ? 'selected' : '' }}>3+ Stars</option>
                </select>
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="btn-primary">Filter</button>
                <a href="{{ route('toolbox-talks.feedback') }}" class="btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Feedback List -->
    <div class="bg-white border border-border-gray rounded overflow-hidden">
        <div class="p-4 border-b border-border-gray">
            <h2 class="text-lg font-semibold text-primary-black">All Feedback</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Talk</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Employee</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Rating</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Sentiment</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-gray">
                    @forelse($feedback as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-primary-black">{{ $item->toolboxTalk->title }}</div>
                                <div class="text-xs text-medium-gray">{{ $item->toolboxTalk->reference_number }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $item->employee_name ?? 'Anonymous' }}</td>
                            <td class="px-4 py-3 text-sm">
                                {!! $item->getFeedbackTypeBadge() !!}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($item->overall_rating)
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $item->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                        <span class="ml-1 text-medium-gray">({{ $item->overall_rating }}/5)</span>
                                    </div>
                                @else
                                    <span class="text-medium-gray">No rating</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {!! $item->getSentimentBadge() !!}
                            </td>
                            <td class="px-4 py-3 text-sm text-medium-gray">
                                {{ $item->created_at->format('M d, Y') }}<br>
                                <span class="text-xs">{{ $item->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('toolbox-talks.show', $item->toolboxTalk) }}" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-medium-gray">No feedback found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $feedback->links() }}
    </div>
</div>
@endsection
