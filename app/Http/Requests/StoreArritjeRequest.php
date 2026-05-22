<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArritjeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:kompanite,id',
            'milestone_at' => 'nullable|date',
            'milestone_code' => 'nullable|string|max:100',
            'source_url' => 'nullable|url|max:500',
            'source_description' => 'nullable|string',
        ];
    }
}
