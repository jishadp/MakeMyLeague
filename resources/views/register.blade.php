<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{config('app.name')}}</title>
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
                <p class="text-gray-600">Join {{config('app.name')}} and start managing your cricket leagues</p>
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
                            <div class="flex space-x-2">
                                <!-- Country Code Dropdown -->
                                <div class="w-1/3">
                                    <select id="country_code" name="country_code" 
                                            class="glass-input block w-full rounded-lg text-base py-3 px-3 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50"
                                            aria-label="Select country code">
                                        <!-- Options will be populated by JavaScript -->
                                    </select>
                                </div>
                                <!-- Phone Number Input -->
                                <div class="w-2/3">
                                    <input id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}"
                                           class="glass-input block w-full rounded-lg text-base py-3 px-4 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50"
                                           required
                                           placeholder="Enter your mobile number">
                                </div>
                            </div>
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
    <script src="{{ asset('js/countries.js') }}?v={{ time() }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'classic',
                width: '100%'
            });

            // Initialize country code dropdown
            initializeCountryDropdown();
        });

        function initializeCountryDropdown() {
            const countrySelect = document.getElementById('country_code');
            const mobileInput = document.getElementById('mobile');
            
            // Clear existing options
            countrySelect.innerHTML = '';
            
            // Add countries to dropdown
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.code;
                option.textContent = `${country.flag} ${country.name} (${country.code})`;
                
                // Set India as default
                if (country.name === 'India') {
                    option.selected = true;
                }
                
                countrySelect.appendChild(option);
            });
            
            // Handle form validation errors - extract country code from old mobile value
            const oldMobileValue = mobileInput.value;
            if (oldMobileValue && oldMobileValue.length > 10) {
                // Try to find matching country code
                const matchingCountry = countries.find(country => 
                    oldMobileValue.startsWith(country.code)
                );
                
                if (matchingCountry) {
                    countrySelect.value = matchingCountry.code;
                    // Remove country code from mobile input
                    mobileInput.value = oldMobileValue.substring(matchingCountry.code.length);
                }
            }
            
            // Handle country selection change
            countrySelect.addEventListener('change', function() {
                const selectedCountry = countries.find(country => country.code === this.value);
                if (selectedCountry) {
                    // Update placeholder with selected country code
                    mobileInput.placeholder = `Enter your mobile number`;
                    
                    // Focus on mobile input for better UX
                    mobileInput.focus();
                }
            });
            
            // Add keyboard navigation support
            countrySelect.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.focus();
                }
            });
        }
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

        /* Country Code Dropdown Styling */
        #country_code {
            font-size: 14px;
            line-height: 1.2;
        }

        #country_code option {
            padding: 8px 12px;
            font-size: 14px;
        }

        /* Responsive adjustments for mobile */
        @media (max-width: 768px) {
            .flex.space-x-2 {
                flex-direction: column;
                gap: 8px;
            }
            
            .w-1\/3, .w-2\/3 {
                width: 100% !important;
            }
            
            #country_code {
                font-size: 16px; /* Prevent zoom on iOS */
            }
        }

        /* Focus states for better accessibility */
        #country_code:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        /* Hover effect for dropdown options */
        #country_code option:hover {
            background-color: #f3f4f6;
        }
    </style>
</body>
</html>
