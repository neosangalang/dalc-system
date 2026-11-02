<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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
        $studentId = $this->route('student') ?? $this->route('id') ?? $this->id;

        return [
            'first_name'                => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.]+$/u'],
            'last_name'                 => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.]+$/u'],
            'learners_reference_number' => ['required', 'digits:12', 'unique:students,learners_reference_number,' . $studentId],
            'class_name'                => ['required', 'string', 'max:255'],
            'date_of_birth'             => ['required', 'date', 'before:today', 'after:1950-01-01'],
            'gender'                    => ['required', 'in:Male,Female'],
            'diagnosis'                 => ['required', 'string'],
            'diagnosis_other'           => ['required_if:diagnosis,Other', 'nullable', 'string', 'max:255'],
            'teacher_id'                => ['required', Rule::exists('users', 'id')->where('role', 'teacher')],
            'guardian_id'               => ['nullable', Rule::exists('users', 'id')->where('role', 'guardian')],
            'status'                    => ['required', 'in:active,archived'],
            'documents'                 => ['nullable', 'array'],
            'documents.*'               => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'medical_document'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'deleted_documents'         => ['nullable', 'array'],
            'deleted_documents.*'       => ['integer'],
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
            'first_name.regex'            => 'The first name may only contain letters, spaces, hyphens, and periods.',
            'last_name.regex'             => 'The last name may only contain letters, spaces, hyphens, and periods.',
            'date_of_birth.before'        => 'The date of birth must be a past date.',
            'date_of_birth.after'         => 'The date of birth must be a valid, realistic date.',
            'teacher_id.exists'           => 'The selected teacher must be an active teacher account.',
            'guardian_id.exists'          => 'The selected guardian must be an active guardian account.',
            'diagnosis_other.required_if' => 'Please specify the diagnosis when selecting Other.',
        ];
    }
}

