<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRaundiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:kompanite,id',
            'funding_round_id' => 'nullable|string|max:100',
            'funded_at' => 'nullable|date',
            'funding_round_type' => 'required|string|in:seed,series-a,series-b,series-c,
            angel,grant,debt_financing,private_equity,undisclosed',
            'funding_round_code' => 'nullable|string|max:50',
            'raised_amount_usd' => 'nullable|numeric|min:0',
            'raised_amount' => 'nullable|numeric|min:0',
            'raised_currency_code' => 'nullable|string|max:10',
            'pre_money_valuation_usd' => 'nullable|numeric|min:0',
        ];
    }
}
