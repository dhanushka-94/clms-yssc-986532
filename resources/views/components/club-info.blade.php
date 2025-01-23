@props(['showDetails' => false])

<div class="club-info card">
    <div class="card-header flex items-center space-x-4">
        <x-club-logo size="large" />
        <div>
            <h2 class="text-2xl font-bold">{{ config('club.name') }}</h2>
            <p class="text-sm text-yellow-100">Est. {{ config('club.established') }}</p>
        </div>
    </div>

    @if($showDetails)
    <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h3 class="text-lg font-semibold mb-2">Contact Information</h3>
            <div class="space-y-2">
                <p class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ config('club.address') }}
                </p>
                <p class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ config('club.phone') }}
                </p>
                <p class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ config('club.email') }}
                </p>
                <p class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    {{ config('club.website') }}
                </p>
            </div>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-2">Club Colors</h3>
            <div class="flex flex-wrap gap-2">
                @foreach(config('club.colors') as $name => $color)
                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full mr-2" style="background-color: {{ $color }}"></div>
                        <span class="text-sm text-gray-600 capitalize">{{ $name }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div> 