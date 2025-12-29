<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 1. Ambil ID student dari URL (hanya ada pas Update)
        $studentId = $this->route('student'); 

        // 2. Rules Dasar (Berlaku untuk Create & Update)
        $rules = [
            'name' => 'required|string|max:255',
        ];

        // 3. Logic Pembeda (POST vs PATCH)
        if ($this->isMethod('post')) {
            // === LOGIC CREATE ===
            $rules['student_id'] = 'required|unique:students,student_id';
            $rules['email']      = 'required|email|unique:students,email';
            
        } else {
            // === LOGIC UPDATE ===
            $rules['student_id'] = [
                'required', 
                Rule::unique('students')->ignore($studentId) // Abaikan ID sendiri
            ];
            $rules['email'] = [
                'required', 
                'email',
                Rule::unique('students')->ignore($studentId) // Abaikan ID sendiri
            ];
        }

        return $rules;
    }
}