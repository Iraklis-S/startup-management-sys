<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlerjeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'acquiring_company_id' => 'required|integer|exists:kompanite,id',
            'acquired_company_id' => 'required|integer|exists:kompanite,id|different:acquiring_company_id',
            'acquisition_id' => 'nullable|string|max:100',
            'term_code' => 'nullable|string|max:50',
            'price_amount' => 'nullable|numeric|min:0',
            'price_currency_code' => 'nullable|string|max:10',
            'acquired_at' => 'nullable|date',
            'source_url' => 'nullable|url|max:500',
            'source_description' => 'nullable|string',
        ];
    }
}
