<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title'         => ['sometimes', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'source'        => ['nullable', 'string', 'max:500'],
            'category_id'   => ['sometimes', 'integer', 'exists:categories,id'],
            'difficulty_id' => ['sometimes', 'integer', 'exists:difficulties,id'],
            'prep_time'     => ['sometimes', 'integer', 'min:1'],
            'cook_time'     => ['sometimes', 'integer', 'min:0'],
            'servings'      => ['sometimes', 'integer', 'min:1'],
            'instructions'  => ['sometimes', 'string'],
            'image'         => ['nullable', 'image', 'max:2048'],
            'ingredients'            => ['sometimes', 'array', 'min:1'],
            'ingredients.*.id'       => ['required_with:ingredients', 'integer', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required_with:ingredients', 'string'],
            'ingredients.*.unit'     => ['nullable', 'string'],
        ];
    }
}
