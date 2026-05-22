<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarredhenieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'person_id' => 'required|integer|exists:personat,id',
            'company_id' => 'required|integer|exists:kompanite,id',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'is_past' => 'boolean',
            'sequence' => 'nullable|integer',
            'title' => 'nullable|string|max:150',
        ];
    }
}
