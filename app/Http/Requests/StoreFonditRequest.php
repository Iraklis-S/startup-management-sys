<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFonditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:kompanite,id',
            'name' => 'required|string|max:255',
            'fund_id' => 'nullable|string|max:100',
            'funded_at' => 'nullable|date',
            'raised_amount' => 'nullable|numeric|min:0',
            'raised_currency_code' => 'nullable|string|max:10',
            'source_url' => 'nullable|url|max:500',
            'source_description' => 'nullable|string',
        ];
    }
}
