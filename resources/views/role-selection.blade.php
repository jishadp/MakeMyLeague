<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Role - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/glacier-blue-theme.css') }}?v={{ time() }}">
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}

</head>

<body class="bg-gray-50 antialiased min-h-screen glacier-blue-theme" id="app-body">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#4a90e2] drop-shadow"
                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Choose Your Role</h2>
                <p class="text-gray-600">Select your role in the cricket league system</p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-50 text-green-600 p-4 rounded-lg border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Role Selection Form Card -->
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

                <form method="POST" action="{{ route('role-selection.store') }}" class="space-y-6">
                    @csrf

                    <!-- Role Selection -->
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 mb-4">Select Your Role <span
                                class="text-red-600">*</span></label>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach ($roles as $role)
                                <div class="relative">
                                    <input type="radio" id="role_{{ $role->id }}" name="role_id"
                                        value="{{ $role->id }}" data-role="{{ $role->name }}"
                                        class="sr-only peer" required>

                                    <label for="role_{{ $role->id }}"
                                        class="role-card flex flex-col items-center justify-center p-6 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-[#4a90e2] peer-checked:bg-gradient-to-br peer-checked:from-[#4a90e2] peer-checked:to-[#87ceeb] peer-checked:text-white peer-checked:shadow-lg peer-checked:scale-105 hover:border-[#4a90e2] hover:shadow-md hover:scale-102 transition-all duration-300 glass-card">

                                        <!-- Role Icon -->
                                        <div class="mb-3">
                                            @switch($role->name)
                                                @case('organizer')
                                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                                                    </svg>
                                                @break

                                                @case('owner')
                                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @break

                                                @case('player')
                                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @break

                                                @default
                                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                            @endswitch
                                        </div>

                                        <!-- Role Name -->
                                        <span
                                            class="text-sm font-medium text-center capitalize">{{ $role->name }}</span>

                                        <!-- Role Description -->
                                        <div class="mt-2 text-xs text-center opacity-75">
                                            @switch($role->name)
                                                @case('organizer')
                                                    Create and manage leagues
                                                @break

                                                @case('owner')
                                                    Own and manage teams
                                                @break

                                                @case('player')
                                                    Participate in leagues
                                                @break

                                                @default
                                                    System role
                                            @endswitch
                                        </div>

                                        <!-- Check Icon (hidden by default, shown when selected) -->
                                        <svg class="absolute top-3 right-3 w-6 h-6 text-white opacity-0 peer-checked:opacity-100 transition-all duration-300 peer-checked:scale-110"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Local Body Field (hidden by default) -->
                    <div id="localBodyField" class="hidden mt-6">
                        <label for="local_body" class="block text-sm font-medium text-gray-700 mb-2 ">
                            Local Body <span class="text-red-600">*</span>
                        </label>
                        <select id="local_body" name="local_body_id" required
                            class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                            <option value="">Select your local body</option>
                            @foreach ($localBodies as $localBody)
                                <option value="{{ $localBody->id }}">{{ $localBody->name }}</option>
                            @endforeach

                        </select>
                    </div>


                    <!-- Continue Button -->
                    <div class="pt-6">
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-[#4a90e2] to-[#87ceeb] hover:from-[#3a80d2] hover:to-[#77bee1] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4a90e2] transition-all duration-200">
                            Continue to Dashboard
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-in-out;
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.4s ease-in-out;
        }

        /* Custom scale classes */
        .hover\:scale-102:hover {
            transform: scale(1.02);
        }

        /* Enhanced role selection styling */
        .role-card {
            position: relative;
            overflow: hidden;
        }

        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #4a90e2 0%, #87ceeb 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .peer:checked+.role-card::before {
            opacity: 1;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" ></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const radios = document.querySelectorAll("input[name='role_id']");
            const localBodyField = document.getElementById("localBodyField");
            const localBodySelect = document.getElementById("local_body");

            function toggleLocalBody() {
                const selected = document.querySelector("input[name='role_id']:checked");
                if (selected && selected.dataset.role === "Player") {
                    localBodyField.classList.remove("hidden");
                    localBodySelect.setAttribute("required", "required");
                } else {
                    localBodyField.classList.add("hidden");
                    localBodySelect.removeAttribute("required");
                    localBodySelect.value = ""; // optional: reset selection if switching away
                }
            }

            radios.forEach(radio => {
                radio.addEventListener("change", toggleLocalBody);
            });

            // Initial check in case form reloads with player selected
            toggleLocalBody();
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select Local Body ',
                allowClear: true
            });
        });
    </script>

</body>

</html>
