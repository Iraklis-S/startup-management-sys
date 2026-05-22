<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestimitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'funding_round_id' => 'required|integer|exists:raundet_financimit,id',
            'funded_company_id' => 'required|integer|exists:kompanite,id',
            'investor_company_id' => 'required|integer|exists:kompanite,id|different:funded_company_id',
        ];
    }
}
