<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->student->user_id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'admission_number' => ['nullable', 'string', 'max:255', Rule::unique('students', 'admission_number')->ignore($this->student->id)],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable', 'exists:parents,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
        ];
    }
}
