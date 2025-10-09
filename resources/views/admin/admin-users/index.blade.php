@extends('layouts.app')

@section('title', 'Admin Users Management | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Admin Users Management</h1>
                    <p class="text-gray-600 mt-2">Manage admin users and their access</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.admin-users.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Admin User
                    </a>
                    <a href="{{ route('admin.organizer-requests.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Admin
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form action="{{ route('admin.admin-users.index') }}" method="GET">
                <div class="flex items-center gap-4">
                    <label for="search" class="text-sm font-medium text-gray-700">Search:</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Name, Mobile, or Email"
                           class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.admin-users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Admin Users Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Admin Users ({{ $adminUsers->total() }} total)</h2>
            </div>
            
            @if($adminUsers->isEmpty())
                <div class="p-12 text-center">
                    <div class="mb-6">
                        <svg class="w-20 h-20 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Admin Users Found</h3>
                    <p class="text-gray-600 mb-6">No admin users found matching your criteria.</p>
                    <a href="{{ route('admin.admin-users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create First Admin User
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($adminUsers as $adminUser)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if($adminUser->photo)
                                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ asset($adminUser->photo) }}" alt="{{ $adminUser->name }}">
                                                @else
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-red-600 to-pink-600 flex items-center justify-center">
                                                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($adminUser->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $adminUser->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $adminUser->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $adminUser->mobile }}</div>
                                        <div class="text-sm text-gray-500">{{ $adminUser->email ?? 'No email' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @foreach($adminUser->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $adminUser->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.admin-users.show', $adminUser) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                               title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.admin-users.edit', $adminUser) }}" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors"
                                               title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <button onclick="resetAdminPin('{{ $adminUser->slug }}', '{{ $adminUser->name }}')" 
                                                    class="text-orange-600 hover:text-orange-900 transition-colors"
                                                    title="Reset PIN">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </button>
                                            @if($adminUser->id !== auth()->id())
                                                <form action="{{ route('admin.admin-users.destroy', $adminUser) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this admin user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs" title="You cannot delete your own account">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($adminUsers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $adminUsers->links() }}
                    </div>
                @endif
            @endif
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
