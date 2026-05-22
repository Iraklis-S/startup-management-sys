<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKompaniaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $kompaniaParam = $this->route('kompania');
        $kompaniaId = is_object($kompaniaParam) ? $kompaniaParam->id : (is_numeric($kompaniaParam) ? (int)$kompaniaParam : null);

        return [
            'name' => 'required|string|max:255',
            'company_type' => 'nullable|string|in:startup,other',
            'parent_id' => 'nullable|integer|exists:kompanite,id',
            'normalized_name' => 'nullable|string|max:255',
            'permalink' => [
                'nullable','string','max:255',
                Rule::unique('kompanite','permalink')->ignore($kompaniaId),
            ],
            'category_code' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:operating,closed,acquired,ipo',
            'founded_at' => 'nullable|date',
        ];
    }
}
