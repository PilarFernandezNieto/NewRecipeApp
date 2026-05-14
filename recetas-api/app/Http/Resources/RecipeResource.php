<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'description' => $this->description,
            'source'      => $this->source,
            'instructions' => $this->instructions,
            'image'       => $this->image ? asset('storage/' . $this->image) : null,
            'prep_time'   => $this->prep_time,
            'cook_time'   => $this->cook_time,
            'servings'    => $this->servings,
            'category'    => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'difficulty'  => [
                'id'   => $this->difficulty->id,
                'name' => $this->difficulty->name,
                'slug' => $this->difficulty->slug,
            ],
            'ingredients' => $this->ingredients->map(fn ($ingredient) => [
                'id'       => $ingredient->id,
                'name'     => $ingredient->name,
                'quantity' => $ingredient->pivot->quantity,
                'unit'     => $ingredient->pivot->unit,
            ]),
            'avg_rating'      => round($this->ratings->avg('score'), 1),
            'ratings_count'   => $this->ratings->count(),
            'is_favorited'    => $request->user()
                ? $this->favoritedBy->contains($request->user()->id)
                : false,
            'created_at'  => $this->created_at->toDateTimeString(),
            'updated_at'  => $this->updated_at->toDateTimeString(),
        ];
    }
}
