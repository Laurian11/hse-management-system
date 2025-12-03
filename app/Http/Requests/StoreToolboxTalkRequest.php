<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreToolboxTalkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'topic_id' => 'nullable|exists:toolbox_talk_topics,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:5|max:60',
            'location' => 'required|string|max:255',
            'talk_type' => 'required|in:safety,health,environment,incident_review,custom',
            'biometric_required' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'required_if:is_recurring,1|in:daily,weekly,monthly',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The toolbox talk title is required.',
            'scheduled_date.required' => 'Please select a scheduled date.',
            'scheduled_date.after_or_equal' => 'The scheduled date cannot be in the past.',
            'start_time.required' => 'Please specify a start time.',
            'duration_minutes.required' => 'Please specify the duration.',
            'duration_minutes.min' => 'Duration must be at least 5 minutes.',
            'duration_minutes.max' => 'Duration cannot exceed 60 minutes.',
            'location.required' => 'Please specify the location.',
        ];
    }
}

