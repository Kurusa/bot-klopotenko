@include('recipes.header', ['recipe' => $recipe])

@include('recipes.ingredients', ['ingredients' => $recipe->ingredients_collection])

@if ($finishedRecipeRating)
{{ $finishedRecipeRating }}
@endif
