<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id'           => ['required', 'exists:students,id'],
            'log_date'             => ['required', 'date', 'before_or_equal:today'],
            'quarter'              => ['required', 'in:Q1,Q2,Q3,Q4'],
            'raw_notes'            => ['nullable', 'string', 'max:5000'],
            'notes'                => ['nullable', 'string', 'max:5000'],
            'home_recommendations' => ['nullable', 'string', 'max:2000'],
            'photo'                => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'log_date.before_or_equal' => 'The observation date cannot be a future date.',
            'quarter.in'               => 'The academic quarter must be Q1, Q2, Q3, or Q4.',
            'home_recommendations.max' => 'Home recommendations may not exceed 2,000 characters.',
        ];
    }
}

