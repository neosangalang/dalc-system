<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
            'report_type'          => ['required', 'in:daily,q1,q2,q3,q4,mid_year,year_end'],
            'report_date'          => ['nullable', 'date', 'before_or_equal:today'],
            'content'              => ['required', 'string', 'min:5'],
            'home_recommendations' => ['nullable', 'string', 'max:2000'],
            'action'               => ['required', 'in:draft,submit'],
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
            'report_type.in'              => 'Please select a valid report classification.',
            'report_date.before_or_equal' => 'Report date cannot be in the future.',
            'content.min'                 => 'Report narrative must be at least 5 characters.',
            'home_recommendations.max'    => 'Home recommendations may not exceed 2,000 characters.',
        ];
    }
}

