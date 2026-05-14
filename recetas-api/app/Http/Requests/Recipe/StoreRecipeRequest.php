<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'source'        => ['nullable', 'string', 'max:500'],
            'category_id'   => ['required', 'integer', 'exists:categories,id'],
            'difficulty_id' => ['required', 'integer', 'exists:difficulties,id'],
            'prep_time'     => ['required', 'integer', 'min:1'],
            'cook_time'     => ['required', 'integer', 'min:0'],
            'servings'      => ['required', 'integer', 'min:1'],
            'instructions'  => ['required', 'string'],
            'image'         => ['nullable', 'image', 'max:2048'],
            'ingredients'           => ['required', 'array', 'min:1'],
            'ingredients.*.id'      => ['required', 'integer', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required', 'string'],
            'ingredients.*.unit'    => ['nullable', 'string'],
        ];
    }
}
