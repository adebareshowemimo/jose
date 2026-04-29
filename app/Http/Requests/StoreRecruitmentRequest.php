<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecruitmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->company !== null;
    }

    public function rules(): array
    {
        return [
            'service_type' => 'required|in:cv_sourcing,partial_recruitment,full_recruitment',
            'cv_count' => 'required|integer|min:1|max:50',

            'job_title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'job_type_id' => 'nullable|exists:job_types,id',
            'location_id' => 'nullable|exists:locations,id',
            'experience_level' => 'nullable|string|max:50',

            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|gte:salary_min',
            'salary_currency' => 'nullable|string|size:3',

            'description' => 'required|string|max:5000',
            'skills' => 'nullable|array|max:50',
            'skills.*' => 'nullable|string|max:120',
            'certificates' => 'nullable|array|max:50',
            'certificates.*.name' => 'nullable|string|max:200',
            'certificates.*.vendor' => 'nullable|string|max:200',
            'certificates.*.issued_at' => 'nullable|date',
            'certificates.*.expires_at' => 'nullable|date|after_or_equal:certificates.*.issued_at',
            'needed_by' => 'nullable|date|after_or_equal:today',

            'jd_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'service_type.required' => 'Please choose a service tier.',
            'cv_count.min' => 'You must request at least 1 CV.',
            'cv_count.max' => 'For requests over 50 CVs, please contact us directly.',
            'job_title.required' => 'A job title or role name is required.',
            'description.required' => 'Please describe the role and requirements.',
            'salary_max.gte' => 'Maximum salary must be greater than or equal to minimum.',
            'jd_file.mimes' => 'Job description must be a PDF, DOC, or DOCX file.',
            'jd_file.max' => 'Job description file must be 5 MB or smaller.',
            'certificates.*.expires_at.after_or_equal' => 'Certificate expiry must be on or after the date received.',
        ];
    }
}
