@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Team</h1>
        <p class="mt-2 text-gray-600">Update the details for {{ $team->name }}</p>
    </div>

    <!-- Edit Team Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('teams.update', $team) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Team Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Team Name <span class="text-red-600">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $team->name) }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#4a90e2] focus:ring focus:ring-[#4a90e2] focus:ring-opacity-50"
                       placeholder="Enter team name">
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Logo & Banner Upload -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Logo Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Team Logo</label>
                    <div class="space-y-4">
                        @if($team->logo)
                            <div class="flex items-center space-x-4">
                                <img src="{{ Storage::url($team->logo) }}" alt="Current Logo" class="w-16 h-16 object-cover rounded-lg border">
                                <div>
                                    <p class="text-sm text-gray-600">Current Logo</p>
                                    <button type="button" onclick="removeTeamLogo()" class="text-red-600 text-sm hover:text-red-800">Remove</button>
                                </div>
                            </div>
                        @endif
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="team-logo-upload" accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('team-logo-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Click to upload logo</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB (1:1 ratio recommended)</p>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Banner Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Team Banner</label>
                    <div class="space-y-4">
                        @if($team->banner)
                            <div class="flex items-center space-x-4">
                                <img src="{{ Storage::url($team->banner) }}" alt="Current Banner" class="w-24 h-16 object-cover rounded-lg border">
                                <div>
                                    <p class="text-sm text-gray-600">Current Banner</p>
                                    <button type="button" onclick="removeTeamBanner()" class="text-red-600 text-sm hover:text-red-800">Remove</button>
                                </div>
                            </div>
                        @endif
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="team-banner-upload" accept="image/*" class="hidden">
                            <button type="button" onclick="document.getElementById('team-banner-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Click to upload banner</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB (wide format recommended)</p>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Home Ground -->
                <div>
                    <label for="home_ground_id" class="block text-sm font-medium text-gray-700 mb-1">Home Ground <span class="text-red-600">*</span></label>
                    <select name="home_ground_id" id="home_ground_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#4a90e2] focus:ring focus:ring-[#4a90e2] focus:ring-opacity-50">
                        <option value="">Select Home Ground</option>
                        @foreach($grounds as $ground)
                            <option value="{{ $ground->id }}" {{ old('home_ground_id', $team->home_ground_id) == $ground->id ? 'selected' : '' }}>
                                {{ $ground->name }} ({{ $ground->localBody->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('home_ground_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Local Body -->
                <div>
                    <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-1">Local Body <span class="text-red-600">*</span></label>
                    <select name="local_body_id" id="local_body_id" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#4a90e2] focus:ring focus:ring-[#4a90e2] focus:ring-opacity-50">
                        <option value="">Select Local Body</option>
                        @foreach($localBodies as $localBody)
                            <option value="{{ $localBody->id }}" {{ old('local_body_id', $team->local_body_id) == $localBody->id ? 'selected' : '' }}>
                                {{ $localBody->name }} ({{ $localBody->district->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('local_body_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('teams.show', $team) }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-[#4a90e2] to-[#87ceeb] text-white py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    Update Team
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Team Image Upload and Cropper.js functionality
    let teamLogoCropper, teamBannerCropper;
    let currentTeamUploadType = '';

    // Team logo upload handler
    document.getElementById('team-logo-upload').addEventListener('change', function(e) {
        handleTeamImageUpload(e, 'logo');
    });

    // Team banner upload handler
    document.getElementById('team-banner-upload').addEventListener('change', function(e) {
        handleTeamImageUpload(e, 'banner');
    });

    function handleTeamImageUpload(event, type) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size
        const maxSize = type === 'logo' ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for logo, 5MB for banner
        if (file.size > maxSize) {
            alert(`File size must be less than ${type === 'logo' ? '2MB' : '5MB'}`);
            return;
        }

        currentTeamUploadType = type;
        const reader = new FileReader();
        reader.onload = function(e) {
            showTeamCropperModal(e.target.result, type);
        };
        reader.readAsDataURL(file);
    }

    function showTeamCropperModal(imageSrc, type) {
        // Create modal HTML
        const modalHtml = `
            <div id="team-cropper-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">Crop Team ${type === 'logo' ? 'Logo' : 'Banner'}</h3>
                        <button type="button" onclick="closeTeamCropperModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <img id="team-cropper-image" src="${imageSrc}" style="max-width: 100%; max-height: 400px;">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeTeamCropperModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="button" onclick="cropAndUploadTeam()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Crop & Upload
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Initialize cropper
        const image = document.getElementById('team-cropper-image');
        
        if (type === 'logo') {
            teamLogoCropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.8,
                restore: false,
                guides: false,
                center: false,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        } else {
            teamBannerCropper = new Cropper(image, {
                aspectRatio: 16/9,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.8,
                restore: false,
                guides: false,
                center: false,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        }
    }

    function closeTeamCropperModal() {
        const modal = document.getElementById('team-cropper-modal');
        if (modal) {
            modal.remove();
        }
        if (teamLogoCropper) {
            teamLogoCropper.destroy();
            teamLogoCropper = null;
        }
        if (teamBannerCropper) {
            teamBannerCropper.destroy();
            teamBannerCropper = null;
        }
        currentTeamUploadType = '';
    }

    function cropAndUploadTeam() {
        const cropper = currentTeamUploadType === 'logo' ? teamLogoCropper : teamBannerCropper;
        if (!cropper) return;

        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            width: currentTeamUploadType === 'logo' ? 300 : 800,
            height: currentTeamUploadType === 'logo' ? 300 : 450,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Convert to blob
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append(currentTeamUploadType, blob, `${currentTeamUploadType}.jpg`);

            // Show loading
            const uploadBtn = document.querySelector('#team-cropper-modal button:last-child');
            const originalText = uploadBtn.textContent;
            uploadBtn.textContent = 'Uploading...';
            uploadBtn.disabled = true;

            // Upload to server
            fetch(`/teams/{{ $team->slug }}/upload-${currentTeamUploadType}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response is not JSON:', text);
                        throw new Error('Server returned non-JSON response');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    // Reload page to show new image
                    location.reload();
                } else {
                    alert('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed: ' + error.message);
            })
            .finally(() => {
                uploadBtn.textContent = originalText;
                uploadBtn.disabled = false;
            });
        }, 'image/jpeg', 0.8);
    }

    function removeTeamLogo() {
        if (confirm('Are you sure you want to remove the logo?')) {
            fetch(`/teams/{{ $team->slug }}/remove-logo`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to remove logo');
                }
            });
        }
    }

    function removeTeamBanner() {
        if (confirm('Are you sure you want to remove the banner?')) {
            fetch(`/teams/{{ $team->slug }}/remove-banner`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to remove banner');
                }
            });
        }
    }
</script>

<!-- Include Cropper.js CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
@endsection
