<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRecruitmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quoted_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'quote_note' => 'nullable|string|max:2000',
        ];
    }
}
