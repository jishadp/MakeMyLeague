@extends('layouts.app')

@section('title', 'Profile - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-indigo-600 to-purple-600 py-4 px-4 sm:px-6 lg:px-8 text-white animate-fadeIn">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-4 drop-shadow">
                My Profile
            </h1>
            <p class="text-lg sm:text-xl text-white-100">
                Manage your account details and preferences
            </p>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Photo Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-8 text-center">
                    <div class="relative inline-block">
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg mx-auto">
                            @if($user->photo)
                                <img id="profile-image" src="{{ Storage::url($user->photo) }}" alt="Profile" class="w-full h-full object-cover">
                            @else
                                <div id="profile-placeholder" class="w-full h-full bg-gray-300 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="document.getElementById('photo-input').click()" 
                                class="absolute -bottom-2 -right-2 bg-white rounded-full p-2 shadow-lg hover:shadow-xl transition-shadow">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                        <input type="file" id="photo-input" accept="image/*" class="hidden" onchange="showCropModal(this)">
                    </div>
                    <h2 class="text-xl font-semibold text-white mt-4">{{ $user->name }}</h2>
                    <p class="text-blue-100 text-sm">{{ $user->email }}</p>
                </div>

                <!-- Account Information -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Member Since</span>
                            <span class="font-medium">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Account Status</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Active</span>
                        </div>
                        @if($user->roles->isNotEmpty())
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Role</span>
                            <span class="font-medium">
                                @if($user->isOrganizer()) 
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">Organizer</span>
                                @elseif($user->isOwner()) 
                                    <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">Team Owner</span>
                                @elseif($user->isPlayer()) 
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Player</span>
                                @else 
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">User</span>
                                @endif
                            </span>
                        </div>
                        @endif
                        @if($user->position)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Position</span>
                            <span class="font-medium">{{ $user->position->name }}</span>
                        </div>
                        @endif
                        @if($user->localBody)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Location</span>
                            <span class="font-medium">{{ $user->localBody->name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Phone</span>
                            <span class="font-medium">{{ $user->country_code }} {{ $user->mobile }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <div class="flex gap-2">
                                    <select name="country_code" class="px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="+91" {{ old('country_code', $user->country_code) == '+91' ? 'selected' : '' }}>+91</option>
                                        <option value="+1" {{ old('country_code', $user->country_code) == '+1' ? 'selected' : '' }}>+1</option>
                                        <option value="+44" {{ old('country_code', $user->country_code) == '+44' ? 'selected' : '' }}>+44</option>
                                    </select>
                                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" 
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                @error('mobile')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Game Information -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Game Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Playing Position</label>
                            <select name="position_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id', $user->position_id) == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Location Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Local Body</label>
                            <select name="local_body_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Local Body</option>
                                @foreach($localBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ old('local_body_id', $user->local_body_id) == $localBody->id ? 'selected' : '' }}>
                                        {{ $localBody->name }} - {{ $localBody->district->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('local_body_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Security</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PIN (Leave blank to keep current)</label>
                            <input type="password" name="pin" placeholder="Enter new PIN" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('pin')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Crop Modal -->
<div id="crop-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">Crop Profile Photo</h3>
            <div class="mb-4">
                <div id="crop-container" class="w-full h-64 bg-gray-100 rounded-lg overflow-hidden"></div>
            </div>
            <div class="flex gap-3">
                <button onclick="closeCropModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                <button onclick="cropAndUpload()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Upload</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<script>
let cropper;
let selectedFile;

function showCropModal(input) {
    if (input.files && input.files[0]) {
        selectedFile = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100%';
            
            const container = document.getElementById('crop-container');
            container.innerHTML = '';
            container.appendChild(img);
            
            cropper = new Cropper(img, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                cropBoxResizable: true,
                cropBoxMovable: true,
                guides: false,
                center: false,
                highlight: false,
                background: false,
                modal: true
            });
            
            document.getElementById('crop-modal').classList.remove('hidden');
        };
        reader.readAsDataURL(selectedFile);
    }
}

function closeCropModal() {
    document.getElementById('crop-modal').classList.add('hidden');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('photo-input').value = '';
}

function cropAndUpload() {
    if (cropper && selectedFile) {
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });
        
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('photo', blob, 'profile.jpg');
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            
            fetch('{{ route("profile.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    location.reload();
                } else {
                    console.error('Upload failed:', data.error);
                    alert('Upload failed: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);
            });
        }, 'image/jpeg', 0.85);
        
        closeCropModal();
    }
}
</script>

<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
</style>
@endsection