<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{config('app.name')}}</title>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Theme CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/glacier-blue-theme.css') }}?v={{ time() }}">
    
    <!-- Select2 CSS -->
    <link href="{{ asset('assets/select2/select2.min.css') }}" rel="stylesheet" />
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

                        <!-- District -->
                        <div>
                            <label for="district_id" class="block text-sm font-medium text-gray-700 mb-2">District <span class="text-red-600">*</span></label>
                            <select id="district_id" name="district_id" 
                                    class="glass-input block w-full rounded-lg text-base py-3 px-4 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50 select2"
                                    required>
                                <option value="">Select your district</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }} - {{ $district->state->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Location (Local Body) -->
                        <div>
                            <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Location <span class="text-red-600">*</span></label>
                            <select id="local_body_id" name="local_body_id" 
                                    class="glass-input block w-full rounded-lg text-base py-3 px-4 border-2 border-[#2c5aa0] focus:border-[#4a90e2] focus:ring-2 focus:ring-[#4a90e2] focus:ring-opacity-50 select2"
                                    required disabled>
                                <option value="">First select a district</option>
                            </select>
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

    <!-- JavaScript Assets -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/countries.js') }}?v={{ time() }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for all select elements
            $('.select2').select2({
                theme: 'default',
                width: '100%',
                placeholder: function() {
                    return $(this).find('option:first').text();
                }
            });

            // Initialize country code dropdown
            initializeCountryDropdown();
            
            // Initialize cascade select functionality
            initializeCascadeSelect();
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

        function initializeCascadeSelect() {
            const districtSelect = $('#district_id');
            const localBodySelect = $('#local_body_id');
            
            // Handle district selection change
            districtSelect.on('change', function() {
                const districtId = this.value;
                
                // Clear local body options
                localBodySelect.empty().append('<option value="">Select your location</option>');
                
                if (districtId) {
                    // Enable local body select
                    localBodySelect.prop('disabled', false);
                    
                    // Fetch local bodies for selected district
                    fetch(`{{ route('api.local-bodies') }}?district_id=${districtId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Populate local body options
                            data.forEach(localBody => {
                                localBodySelect.append(new Option(localBody.name, localBody.id));
                            });
                            
                            // Set old value if exists (for form validation errors)
                            const oldValue = '{{ old("local_body_id") }}';
                            if (oldValue) {
                                localBodySelect.val(oldValue).trigger('change');
                            }
                            
                            // Refresh Select2
                            localBodySelect.trigger('change');
                        })
                        .catch(error => {
                            console.error('Error fetching local bodies:', error);
                            localBodySelect.empty().append('<option value="">Error loading locations</option>');
                            localBodySelect.trigger('change');
                        });
                } else {
                    // Disable local body select
                    localBodySelect.prop('disabled', true);
                    localBodySelect.empty().append('<option value="">First select a district</option>');
                    localBodySelect.trigger('change');
                }
            });
            
            // Handle form validation errors - restore previous selections
            const oldDistrictId = '{{ old("district_id") }}';
            if (oldDistrictId) {
                districtSelect.val(oldDistrictId).trigger('change');
            }
        }
    </script>

    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
        .animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
        
        /* Select2 Custom Styling to Match Theme */
        .select2-container--default .select2-selection--single {
            background-color: white !important;
            border: 2px solid #2c5aa0 !important;
            border-radius: 0.5rem !important;
            height: 48px !important;
            padding: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151 !important;
            line-height: 44px !important;
            padding-left: 16px !important;
            padding-right: 20px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 8px !important;
            top: 2px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4a90e2 !important;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1) !important;
            outline: none !important;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #4a90e2 !important;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1) !important;
        }

        .select2-dropdown {
            border: 2px solid #2c5aa0 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }

        .select2-container--default .select2-results__option {
            padding: 12px 16px !important;
            color: #374151 !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #4a90e2 !important;
            color: white !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e5e7eb !important;
            color: #374151 !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 8px 12px !important;
            margin: 8px !important;
        }

        /* Disabled state styling */
        .select2-container--default .select2-selection--single[aria-disabled=true] {
            background-color: #f9fafb !important;
            border-color: #d1d5db !important;
            color: #9ca3af !important;
        }

        .select2-container--default .select2-selection--single[aria-disabled=true] .select2-selection__rendered {
            color: #9ca3af !important;
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
