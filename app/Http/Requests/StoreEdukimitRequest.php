<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEdukimitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:personat,id',
            'degree_type' => 'nullable|string|max:100',
            'subject' => 'nullable|string|max:150',
            'institution' => 'nullable|string|max:255',
            'graduated_at' => 'nullable|date',
        ];
    }
}
