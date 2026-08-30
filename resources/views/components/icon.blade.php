@props([
    'name' => 'star',
    'class' => 'w-5 h-5',
    'solid' => false,
])

@php
    $dir = $solid ? 'solid' : 'outline';
    $iconPath = base_path("node_modules/heroicons/24/{$dir}/{$name}.svg");
    
    if (file_exists($iconPath)) {
        $svg = file_get_contents($iconPath);
        $svg = preg_replace('/<svg/', '<svg class="' . e($class) . '"', $svg, 1);
        $svg = preg_replace('/class="[^"]*"/', 'class="' . e($class) . '"', $svg, 1);
    } else {
        $svg = '<svg class="' . e($class) . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/></svg>';
    }
@endphp

{!! $svg !!}

