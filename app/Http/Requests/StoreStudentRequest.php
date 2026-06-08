<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'admission_number' => ['nullable', 'string', 'max:255', 'unique:students,admission_number'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable', 'exists:parents,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Student name is required',
            'email.unique' => 'This email address is already registered',
            'password.confirmed' => 'Password confirmation does not match',
            'admission_number.unique' => 'This admission number already exists',
            'date_of_birth.before' => 'Date of birth must be in the past',
        ];
    }
}
