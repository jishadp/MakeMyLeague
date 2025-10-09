@extends('layouts.app')

@section('title', 'Create Admin User | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Admin User</h1>
                    <p class="text-gray-600 mt-2">Add a new admin user to the system</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.admin-users.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Admin Users
                    </a>
                </div>
            </div>
        </div>

        <!-- Create Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('admin.admin-users.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 @error('name') border-red-300 @enderror"
                               placeholder="Enter full name"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobile -->
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               name="mobile" 
                               id="mobile" 
                               value="{{ old('mobile') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 @error('mobile') border-red-300 @enderror"
                               placeholder="Enter mobile number"
                               required>
                        @error('mobile')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 @error('email') border-red-300 @enderror"
                               placeholder="Enter email address">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PIN -->
                    <div>
                        <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">
                            PIN <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="pin" 
                               id="pin" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 @error('pin') border-red-300 @enderror"
                               placeholder="Enter 4-6 digit PIN"
                               minlength="4"
                               maxlength="6"
                               required>
                        <p class="mt-1 text-sm text-gray-500">PIN should be 4-6 digits long</p>
                        @error('pin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm PIN -->
                    <div>
                        <label for="confirm_pin" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm PIN <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="confirm_pin" 
                               id="confirm_pin" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3 @error('confirm_pin') border-red-300 @enderror"
                               placeholder="Confirm PIN"
                               minlength="4"
                               maxlength="6"
                               required>
                        @error('confirm_pin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Admin User Information</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>The admin user will have full access to the admin panel</li>
                                        <li>They can manage other admin users, analytics, and system settings</li>
                                        <li>Make sure to provide the PIN securely to the new admin user</li>
                                        <li>Mobile number will be used for login authentication</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('admin.admin-users.index') }}" 
                       class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Admin User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// PIN confirmation validation
document.getElementById('confirm_pin').addEventListener('input', function() {
    const pin = document.getElementById('pin').value;
    const confirmPin = this.value;
    
    if (pin && confirmPin && pin !== confirmPin) {
        this.setCustomValidity('PINs do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('pin').addEventListener('input', function() {
    const confirmPin = document.getElementById('confirm_pin');
    if (confirmPin.value) {
        confirmPin.dispatchEvent(new Event('input'));
    }
});
</script>
@endsection
