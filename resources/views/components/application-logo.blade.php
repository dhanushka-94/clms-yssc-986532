@props(['class' => ''])

<img src="{{ asset('images/club-logo.png') }}" 
     alt="{{ config('club.name', 'YSSC') }}" 
     {{ $attributes->merge(['class' => $class]) }}>
