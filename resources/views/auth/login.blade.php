<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center p-6">
        <!-- Main Container -->
        <div class="w-full max-w-4xl flex flex-col lg:flex-row rounded-3xl overflow-hidden">
            <!-- Left side - Banner -->
            <div class="lg:w-5/12 bg-gradient-to-br from-yellow-400 to-green-600 p-8 lg:p-12 flex flex-col justify-between relative overflow-hidden rounded-3xl shadow-xl">
                <!-- Decorative Elements -->
                <div class="absolute top-0 left-0 w-full h-full">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-yellow-300/20 rounded-full -mr-20 -mt-20 animate-pulse"></div>
                    <div class="absolute bottom-0 left-0 w-40 h-40 bg-green-500/20 rounded-full -ml-20 -mb-20 animate-pulse"></div>
                </div>

                <!-- Content -->
                <div class="relative z-10 text-white">
                    <div class="flex items-center justify-center mb-8">
                        <x-club-logo size="large" class="w-24 h-24 drop-shadow-xl transform hover:scale-105 transition-transform duration-300" />
                    </div>
                    <h1 class="text-3xl font-bold text-center mb-4 text-shadow">{{ config('club.name') }}</h1>
                    <p class="text-yellow-100/90 text-center text-sm mb-8">Empowering sports excellence through innovation</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 bg-white/10 rounded-xl p-3 hover:bg-white/20 transition-all duration-300 transform hover:translate-x-1">
                            <svg class="w-5 h-5 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="text-sm">Secure and reliable platform</span>
                        </div>
                        <div class="flex items-center space-x-3 bg-white/10 rounded-xl p-3 hover:bg-white/20 transition-all duration-300 transform hover:translate-x-1">
                            <svg class="w-5 h-5 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm">Advanced member management</span>
                        </div>
                        <div class="flex items-center space-x-3 bg-white/10 rounded-xl p-3 hover:bg-white/20 transition-all duration-300 transform hover:translate-x-1">
                            <svg class="w-5 h-5 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">Comprehensive financial tracking</span>
                        </div>
                    </div>
                </div>

                <!-- Developer Credits -->
                <div class="relative z-10 mt-8 text-center">
                    <p class="text-xs text-yellow-100/80">Developed by Olexto Digital Solutions</p>
                </div>
            </div>

            <!-- Right side - Login Form -->
            <div class="lg:w-7/12 p-8 lg:p-12 flex flex-col justify-center">
                <div class="w-full max-w-md mx-auto">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Welcome back!</h2>
                        <p class="mt-2 text-sm text-gray-600">Please sign in to your account</p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div class="group">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-yellow-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    autocomplete="email" 
                                    required 
                                    class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm transition-all duration-200 hover:border-yellow-400"
                                    placeholder="Enter your email"
                                    value="{{ old('email') }}"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="group">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-yellow-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input 
                                    id="password" 
                                    name="password" 
                                    type="password" 
                                    autocomplete="current-password" 
                                    required 
                                    class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 text-sm transition-all duration-200 hover:border-yellow-400"
                                    placeholder="Enter your password"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input 
                                    id="remember_me" 
                                    name="remember" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-yellow-500 focus:ring-yellow-500 border-gray-300 rounded transition-colors duration-200"
                                >
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-500 transition-colors duration-200">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <div>
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-yellow-500 to-green-600 hover:from-yellow-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg">
                                Sign in to your account
                            </button>
                        </div>
                    </form>

                    <!-- Mobile Developer Credits -->
                    <div class="mt-8 pt-6 border-t border-gray-200 text-center lg:hidden">
                        <p class="text-xs text-gray-500">Developed by Olexto Digital Solutions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">&copy; {{ date('Y') }} {{ config('club.name') }}. All rights reserved.</p>
        </div>
    </div>
</x-guest-layout>
