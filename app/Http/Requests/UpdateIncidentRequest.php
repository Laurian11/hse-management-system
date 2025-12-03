<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && 
               $this->route('incident')->company_id === auth()->user()->company_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_type' => 'nullable|in:injury_illness,property_damage,near_miss,environmental,security,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'location_specific' => 'nullable|string|max:255',
            'date_occurred' => 'required|date|before_or_equal:today',
            'status' => 'required|in:reported,open,investigating,resolved,closed',
            'resolution_notes' => 'nullable|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:5120',
            // Injury/Illness fields
            'injury_type' => 'nullable|in:fatal,lost_time,medical_treatment,first_aid,illness,other',
            'body_part_affected' => 'nullable|string|max:255',
            'nature_of_injury' => 'nullable|string|max:255',
            'lost_time_injury' => 'nullable|boolean',
            'days_lost' => 'nullable|integer|min:0',
            'medical_treatment_details' => 'nullable|string',
            // Property Damage fields
            'asset_damaged' => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|numeric|min:0',
            'damage_description' => 'nullable|string',
            'insurance_claim_filed' => 'nullable|boolean',
            // Near Miss fields
            'potential_severity' => 'nullable|in:low,medium,high,critical',
            'potential_consequences' => 'nullable|string',
            'preventive_measures_taken' => 'nullable|string',
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
            'title.required' => 'The incident title is required.',
            'description.required' => 'Please provide a description of the incident.',
            'severity.required' => 'Please select the severity level.',
            'status.required' => 'Please select the incident status.',
            'status.in' => 'Invalid status selected.',
        ];
    }
}

