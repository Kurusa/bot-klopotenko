<b>{{ $recipe->title }}</b>

{{ __('texts.portions', ['portions' => $recipe->portions]) }} | {{ __('texts.time', ['time' => $recipe->time]) }} | @include('recipes.partials.complexity', ['recipe' => $recipe])
