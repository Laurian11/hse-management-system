<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSafetyCommunicationRequest extends FormRequest
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
            'content' => 'required|string',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:digital_signage,mobile_push,email,sms,printed',
            'target_audience' => 'required|array|min:1',
            'target_audience.*' => 'in:all_employees,departments,roles,locations,management',
            'priority' => 'required|in:low,normal,high,urgent',
            'acknowledgment_required' => 'boolean',
            'acknowledgment_deadline' => 'nullable|date|after:today',
            'scheduled_send_at' => 'nullable|date|after:now',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
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
            'title.required' => 'The communication title is required.',
            'content.required' => 'Please provide the communication content.',
            'channels.required' => 'Please select at least one delivery channel.',
            'channels.min' => 'Please select at least one delivery channel.',
            'target_audience.required' => 'Please select the target audience.',
            'target_audience.min' => 'Please select at least one target audience.',
            'priority.required' => 'Please select the priority level.',
        ];
    }
}

