<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{config('app.name')}}</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Theme CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/glacier-blue-theme.css') }}?v={{ time() }}">
</head>
<body class="bg-gray-50 antialiased min-h-screen glacier-blue-theme" id="app-body">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#4a90e2] drop-shadow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                <p class="text-gray-600">Sign in to your {{config('app.name')}} account</p>
            </div>

            <!-- Login Form Card -->
            <div class="glass-card p-8">
                <!-- Status Message -->
                <div id="status" class="mb-6 hidden text-green-600 text-base font-medium text-center">
                    Login successful!
                </div>

                <form method="POST" action="{{ route('do.login')}}" class="space-y-6">
                    @csrf
                    
                    <!-- Mobile -->
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                        <input id="mobile" type="tel" name="mobile"
                               class="glass-input block w-full rounded-lg text-base py-3 px-4 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50"
                               required autofocus autocomplete="tel"
                               placeholder="Enter your mobile number">
                        <p class="mt-2 text-sm text-red-600 hidden" id="mobile-error">Invalid mobile</p>
                    </div>

                    <!-- PIN -->
                    <div>
                        <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN</label>
                        <input id="pin" type="password" name="pin"
                               class="glass-input block w-full rounded-lg text-base py-3 px-4 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50"
                               required autocomplete="current-password"
                               placeholder="Enter your PIN">
                        <p class="mt-2 text-sm text-red-600 hidden" id="pin-error">PIN required</p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-gray-300 text-[#4a90e2] shadow-sm focus:ring-[#4a90e2]" name="remember">
                        <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-[#4a90e2] to-[#87ceeb] hover:from-[#3a80d2] hover:to-[#77bee1] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4a90e2] transition-all duration-200">
                            Sign In
                        </button>
                    </div>

                    <!-- Links -->
                    <div class="flex flex-col sm:flex-row items-center justify-between text-sm">
                        <a href="https://wa.me/918301867613?text=Hi%2C%20I%20forgot%20my%20password%20for%20MakeMyLeague.%20Please%20help%20me%20reset%20it." target="_blank" class="text-[#4a90e2] hover:text-[#3a80d2] font-medium mb-2 sm:mb-0 inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Forgot your password?
                        </a>
                        <a href="{{ route('register') }}" class="text-[#4a90e2] hover:text-[#3a80d2] font-medium">
                            Don't have an account? Register
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
        .animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
    </style>
</body>
</html> 