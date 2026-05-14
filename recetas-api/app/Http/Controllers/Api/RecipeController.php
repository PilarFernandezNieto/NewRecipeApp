<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Http\Resources\RecipeCollection;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    public function index(Request $request): RecipeCollection
    {
        $recipes = Recipe::with(['category', 'difficulty', 'ingredients', 'ratings', 'favoritedBy'])
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->difficulty, fn ($q) => $q->where('difficulty_id', $request->difficulty))
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->ingredient, fn ($q) => $q->whereHas(
                'ingredients',
                fn ($q) => $q->where('ingredients.id', $request->ingredient)
            ))
            ->latest()
            ->paginate($request->per_page ?? 12);

        return new RecipeCollection($recipes);
    }

    public function store(StoreRecipeRequest $request): JsonResponse
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recipes', 'public');
        }

        $recipe = Recipe::create([
            'category_id'   => $request->category_id,
            'difficulty_id' => $request->difficulty_id,
            'title'         => $request->title,
            'slug'          => Str::slug($request->title) . '-' . Str::random(6),
            'description'   => $request->description,
            'source'        => $request->source,
            'instructions'  => $request->instructions,
            'image'         => $imagePath,
            'prep_time'     => $request->prep_time,
            'cook_time'     => $request->cook_time,
            'servings'      => $request->servings,
        ]);

        $ingredients = collect($request->ingredients)->mapWithKeys(fn ($item) => [
            $item['id'] => ['quantity' => $item['quantity'], 'unit' => $item['unit'] ?? null],
        ]);

        $recipe->ingredients()->sync($ingredients);
        $recipe->load(['category', 'difficulty', 'ingredients', 'ratings', 'favoritedBy']);

        return response()->json(new RecipeResource($recipe), 201);
    }

    public function show(Recipe $recipe): RecipeResource
    {
        $recipe->load(['category', 'difficulty', 'ingredients', 'ratings', 'favoritedBy']);

        return new RecipeResource($recipe);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe): JsonResponse
    {
        if ($request->hasFile('image')) {
            if ($recipe->image) {
                Storage::disk('public')->delete($recipe->image);
            }
            $recipe->image = $request->file('image')->store('recipes', 'public');
        }

        $recipe->fill($request->safe()->except(['image', 'ingredients']));
        $recipe->save();

        if ($request->has('ingredients')) {
            $ingredients = collect($request->ingredients)->mapWithKeys(fn ($item) => [
                $item['id'] => ['quantity' => $item['quantity'], 'unit' => $item['unit'] ?? null],
            ]);
            $recipe->ingredients()->sync($ingredients);
        }

        $recipe->load(['category', 'difficulty', 'ingredients', 'ratings', 'favoritedBy']);

        return response()->json(new RecipeResource($recipe));
    }

    public function destroy(Recipe $recipe): JsonResponse
    {
        if ($recipe->image) {
            Storage::disk('public')->delete($recipe->image);
        }

        $recipe->delete();

        return response()->json(['message' => 'Receta eliminada correctamente.']);
    }
}
