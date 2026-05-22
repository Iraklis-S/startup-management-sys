<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersoniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:kompanite,id|unique:personat,company_id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birthplace' => 'nullable|string|max:150',
            'affiliation_name' => 'nullable|string|max:255',
        ];
    }
}
