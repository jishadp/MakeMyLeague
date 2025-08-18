<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-8">
    <div class="w-full max-w-2xl bg-white p-10 rounded-xl shadow-xl">
        <h2 class="text-3xl font-bold text-center mb-8">Create an Account</h2>

        @if ($errors->any())
        <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('do.register') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                           required autofocus>
                </div>

                <!-- Mobile -->
                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                           required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                </div>

                <!-- PIN -->
                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN (4-6 digits)</label>
                    <input id="pin" type="password" name="pin" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                           required minlength="4" maxlength="6">
                </div>

                <!-- Game Role -->
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">Role (Optional)</label>
                    <select id="role_id" name="role_id" 
                            class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Local Body -->
                <div>
                    <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Local Body (Optional)</label>
                    <select id="local_body_id" name="local_body_id" 
                            class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        <option value="">Select Local Body</option>
                        @foreach($localBodies as $localBody)
                            <option value="{{ $localBody->id }}" {{ old('local_body_id') == $localBody->id ? 'selected' : '' }}>
                                {{ $localBody->name }} ({{ $localBody->district->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between">
                <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900 mb-4 sm:mb-0">
                    Already have an account? Login
                </a>
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Register
                </button>
            </div>
        </form>
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
</body>
</html>
