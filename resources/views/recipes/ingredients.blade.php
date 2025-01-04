{!! __('texts.ingredients') !!}
<pre>
@php
$maxLen = $ingredients->max(fn($ingredient) => mb_strlen($ingredient['quantity'] . ' ' . $ingredient['unit']));
@endphp
@foreach ($ingredients as $ingredient)
@php
$lenDiff = $maxLen - mb_strlen($ingredient['quantity'] . ' ' . $ingredient['unit']);
$spaces = str_repeat(' ', $lenDiff);
@endphp
@if($ingredient['quantity'])
- {{ $ingredient['quantity'] }} {{ $ingredient['unit'] }}{{ $spaces }}: {{ $ingredient['title'] }}
@else
- {{ $ingredient['title'] }}
@endif
@endforeach
</pre>
