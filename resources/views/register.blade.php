<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - League Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/glacier-blue-theme.css') }}?v={{ time() }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 antialiased min-h-screen glacier-blue-theme" id="app-body">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#4a90e2] drop-shadow" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Create an Account</h2>
                <p class="text-gray-600">Join League Manager and start managing your cricket leagues</p>
            </div>

            <!-- Register Form Card -->
            <div class="glass-card p-8">
                @if ($errors->any())
                <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-lg border border-red-200">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('do.register') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-600">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                   class="glass-input block w-full rounded-lg text-base py-3 px-4 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50"
                                   required autofocus
                                   placeholder="Enter your full name">
                        </div>

                        <!-- Mobile -->
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number <span class="text-red-600">*</span></label>
                            <input id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}"
                                   class="glass-input block w-full rounded-lg text-base py-3 px-4 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50"
                                   required
                                   placeholder="Enter your mobile number">
                        </div>

                        <!-- PIN -->
                        <div>
                            <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN (4 digits) <span class="text-red-600">*</span></label>
                            <input id="pin" type="password" name="pin" 
                                   class="glass-input block w-full rounded-lg text-base py-3 px-4 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50"
                                   required minlength="4" maxlength="4"
                                   placeholder="Enter 4-digit PIN">
                        </div>

                    </div>

                    <!-- Register Button -->
                    <div class="pt-6">
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-[#4a90e2] to-[#87ceeb] hover:from-[#3a80d2] hover:to-[#77bee1] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4a90e2] transition-all duration-200">
                            Create Account
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-[#4a90e2] hover:text-[#3a80d2] font-medium">
                            Already have an account? Sign in
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic',
                width: '100%'
            });
        });
    </script>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
        .animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
        
        /* Select2 Custom Styling */
        .select2-container--classic .select2-selection--single {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 2px solid #2c5aa0 !important;
            border-radius: 0.5rem !important;
            height: 48px !important;
        }
        
        .select2-container--classic .select2-selection--single .select2-selection__rendered {
            line-height: 48px !important;
            padding-left: 16px !important;
            color: #374151 !important;
        }
        
        .select2-container--classic .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
        }
        
        .select2-container--classic.select2-container--focus .select2-selection--single {
            border-color: #4a90e2 !important;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1) !important;
        }
    </style>
</body>
</html>
