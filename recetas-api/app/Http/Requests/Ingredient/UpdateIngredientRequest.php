<?php

namespace App\Http\Requests\Ingredient;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('ingredients', 'name')->ignore($this->route('ingredient'))],
            'description' => ['nullable', 'string'],
        ];
    }
}
