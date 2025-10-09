@extends('layouts.app')

@section('title', 'Admin User Details | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Admin User Details</h1>
                    <p class="text-gray-600 mt-2">View admin user information and activity</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.admin-users.edit', $adminUser) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Admin User
                    </a>
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

        <!-- Admin User Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-start space-x-6">
                <div class="flex-shrink-0">
                    @if($adminUser->photo)
                        <img class="h-24 w-24 rounded-full object-cover" src="{{ asset($adminUser->photo) }}" alt="{{ $adminUser->name }}">
                    @else
                        <div class="h-24 w-24 rounded-full bg-gradient-to-br from-red-600 to-pink-600 flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">{{ strtoupper(substr($adminUser->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $adminUser->name }}</h2>
                    <p class="text-gray-600 mt-1">Admin User ID: {{ $adminUser->id }}</p>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Mobile Number</h3>
                            <p class="text-sm text-gray-900">{{ $adminUser->mobile }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Email Address</h3>
                            <p class="text-sm text-gray-900">{{ $adminUser->email ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Account Created</h3>
                            <p class="text-sm text-gray-900">{{ $adminUser->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                            <p class="text-sm text-gray-900">{{ $adminUser->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Roles & Permissions</h3>
            <div class="space-y-3">
                @forelse($adminUser->roles as $role)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $role->name }}</h4>
                                <p class="text-sm text-gray-500">Admin role with full system access</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Active
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <p class="text-gray-500 mt-2">No roles assigned</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.admin-users.edit', $adminUser) }}" 
                   class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="bg-blue-100 rounded-full p-2 mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900">Edit Profile</h4>
                        <p class="text-sm text-blue-700">Update admin user information</p>
                    </div>
                </a>

                @if($adminUser->id !== auth()->id())
                    <button onclick="resetAdminPin('{{ $adminUser->slug }}', '{{ $adminUser->name }}')" 
                            class="flex items-center p-4 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition-colors">
                        <div class="bg-orange-100 rounded-full p-2 mr-3">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-orange-900">Reset PIN</h4>
                            <p class="text-sm text-orange-700">Generate new login PIN</p>
                        </div>
                    </button>

                    <form action="{{ route('admin.admin-users.destroy', $adminUser) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this admin user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center p-4 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                            <div class="bg-red-100 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-red-900">Delete Account</h4>
                                <p class="text-sm text-red-700">Permanently remove admin user</p>
                            </div>
                        </button>
                    </form>
                @else
                    <div class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="bg-gray-100 rounded-full p-2 mr-3">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Your Account</h4>
                            <p class="text-sm text-gray-700">You cannot delete your own account</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reset PIN Modal -->
<div id="resetPinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100">
                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Reset Admin PIN</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to reset the PIN for <span id="adminName" class="font-semibold"></span>?
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    A new random 4-digit PIN will be generated and displayed.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmReset" class="px-4 py-2 bg-orange-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    Reset PIN
                </button>
                <button onclick="closeResetPinModal()" class="mt-3 px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">PIN Reset Successful</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    The PIN for <span id="successAdminName" class="font-semibold"></span> has been reset successfully.
                </p>
                <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                    <p class="text-sm text-gray-600">New PIN:</p>
                    <p id="newPin" class="text-lg font-mono font-bold text-indigo-600"></p>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Please inform the admin user of their new PIN.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeSuccessModal()" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAdminSlug = null;

function resetAdminPin(adminSlug, adminName) {
    currentAdminSlug = adminSlug;
    document.getElementById('adminName').textContent = adminName;
    document.getElementById('resetPinModal').classList.remove('hidden');
}

function closeResetPinModal() {
    document.getElementById('resetPinModal').classList.add('hidden');
    currentAdminSlug = null;
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
}

document.getElementById('confirmReset').addEventListener('click', function() {
    if (!currentAdminSlug) return;
    
    // Show loading state
    this.textContent = 'Resetting...';
    this.disabled = true;
    
    fetch(`/admin/admin-users/${currentAdminSlug}/reset-pin`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close reset modal
            closeResetPinModal();
            
            // Show success modal
            document.getElementById('successAdminName').textContent = data.user_name;
            document.getElementById('newPin').textContent = data.new_pin;
            document.getElementById('successModal').classList.remove('hidden');
        } else {
            alert('Error resetting PIN. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error resetting PIN. Please try again.');
    })
    .finally(() => {
        // Reset button state
        this.textContent = 'Reset PIN';
        this.disabled = false;
    });
});
</script>
@endsection
