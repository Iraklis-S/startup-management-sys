<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIpoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:kompanite,id',
            'valuation_amount' => 'nullable|numeric|min:0',
            'valuation_currency_code' => 'nullable|string|max:10',
            'raised_amount' => 'nullable|numeric|min:0',
            'raised_currency_code' => 'nullable|string|max:10',
            'public_at' => 'nullable|date',
            'stock_symbol' => 'nullable|string|max:50',
            'source_url' => 'nullable|url|max:500',
        ];
    }
}
