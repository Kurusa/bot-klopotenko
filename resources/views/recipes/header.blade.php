<b>@include('recipes.partials.title', ['recipe' => $recipe])</b>

{{ __('texts.portions', ['portions' => $recipe->portions]) }} | {{ __('texts.time', ['time' => $recipe->time]) }} | @include('recipes.partials.complexity', ['recipe' => $recipe])
