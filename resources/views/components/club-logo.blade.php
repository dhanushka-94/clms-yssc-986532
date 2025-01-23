@props(['class' => '', 'size' => ''])

@php
    $sizeClasses = [
        'small' => 'h-8 w-8',
        'medium' => 'h-12 w-12',
        'large' => 'h-16 w-16'
    ];

    $classes = $size ? $sizeClasses[$size] ?? '' : $class;
@endphp

<img src="{{ asset('images/club-logo.png') }}" 
     alt="{{ config('club.name', 'YSSC') }}" 
     {{ $attributes->merge(['class' => $classes]) }}> 