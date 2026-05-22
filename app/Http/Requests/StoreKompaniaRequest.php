<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKompaniaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'company_type' => 'nullable|string|in:startup,other',
            'parent_id' => 'nullable|integer|exists:kompanite,id',
            'normalized_name' => 'nullable|string|max:255',
            'permalink' => 'nullable|string|max:255|unique:kompanite,permalink',
            'category_code' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:operating,closed,acquired,ipo',
            'founded_at' => 'nullable|date',
        ];
    }
}
