<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - League Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <p class="text-gray-600">Sign in to your League Manager account</p>
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
                        <a href="#" class="text-[#4a90e2] hover:text-[#3a80d2] font-medium mb-2 sm:mb-0">
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