@extends('layouts.app')

@section('title', 'Cricket Team | ' . config('app.name'))

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teams.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Teams
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create Team</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                @if(request('league_slug'))
                    Create New Team for League
                @else
                    Create New Team
                @endif
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                @if(request('league_slug'))
                    Add a new cricket team that will be automatically added to the league
                @else
                    Add a new cricket team to your collection
                @endif
            </p>
        </div>

        <!-- Create Team Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
            <div class="p-8">
                <form action="{{ route('teams.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @if(request('league_slug'))
                        <input type="hidden" name="league_slug" value="{{ request('league_slug') }}">
                    @endif

                    <!-- Team Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Team Name <span class="text-red-600">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4"
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
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <input type="file" id="create-team-logo-upload" accept="image/*" class="hidden">
                                <button type="button" onclick="document.getElementById('create-team-logo-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">Click to upload logo</p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB (1:1 ratio recommended)</p>
                                </button>
                            </div>
                        </div>

                        <!-- Banner Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Team Banner</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <input type="file" id="create-team-banner-upload" accept="image/*" class="hidden">
                                <button type="button" onclick="document.getElementById('create-team-banner-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">Click to upload banner</p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB (wide format recommended)</p>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Home Ground -->
                        <div>
                            <label for="home_ground_id" class="block text-sm font-medium text-gray-700 mb-2">Home Ground <span class="text-red-600">*</span></label>
                            <select name="home_ground_id" id="home_ground_id" required
                                    class="select2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                                <option value="">Select Home Ground</option>
                                @foreach($grounds as $ground)
                                    <option value="{{ $ground->id }}" {{ old('home_ground_id') == $ground->id ? 'selected' : '' }}>
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
                            <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Local Body <span class="text-red-600">*</span></label>
                            <select name="local_body_id" id="local_body_id" required
                                    class="select2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                                <option value="">Select Local Body</option>
                                @foreach($localBodies as $localBody)
                                    <option value="{{ $localBody->id }}" {{ old('local_body_id') == $localBody->id ? 'selected' : '' }}>
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
                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="{{ route('teams.index') }}" class="bg-gray-200 text-gray-700 py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 font-medium">
                            Create Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Animations -->
<style>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.5s ease-in-out; }
.animate-fadeInUp { animation: fadeInUp 0.4s ease-in-out; }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });
    });

    // Team Create Image Upload and Cropper.js functionality
    let createTeamLogoCropper, createTeamBannerCropper;
    let currentCreateTeamUploadType = '';
    let createTeamLogoFile = null, createTeamBannerFile = null;

    // Create team logo upload handler
    document.getElementById('create-team-logo-upload').addEventListener('change', function(e) {
        handleCreateTeamImageUpload(e, 'logo');
    });

    // Create team banner upload handler
    document.getElementById('create-team-banner-upload').addEventListener('change', function(e) {
        handleCreateTeamImageUpload(e, 'banner');
    });

    function handleCreateTeamImageUpload(event, type) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size
        const maxSize = type === 'logo' ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for logo, 5MB for banner
        if (file.size > maxSize) {
            alert(`File size must be less than ${type === 'logo' ? '2MB' : '5MB'}`);
            return;
        }

        currentCreateTeamUploadType = type;
        const reader = new FileReader();
        reader.onload = function(e) {
            showCreateTeamCropperModal(e.target.result, type);
        };
        reader.readAsDataURL(file);
    }

    function showCreateTeamCropperModal(imageSrc, type) {
        // Create modal HTML
        const modalHtml = `
            <div id="create-team-cropper-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">Crop Team ${type === 'logo' ? 'Logo' : 'Banner'}</h3>
                        <button type="button" onclick="closeCreateTeamCropperModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <img id="create-team-cropper-image" src="${imageSrc}" style="max-width: 100%; max-height: 400px;">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeCreateTeamCropperModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="button" onclick="cropAndSaveCreateTeam()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Crop & Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Initialize cropper
        const image = document.getElementById('create-team-cropper-image');
        
        if (type === 'logo') {
            createTeamLogoCropper = new Cropper(image, {
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
            createTeamBannerCropper = new Cropper(image, {
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

    function closeCreateTeamCropperModal() {
        const modal = document.getElementById('create-team-cropper-modal');
        if (modal) {
            modal.remove();
        }
        if (createTeamLogoCropper) {
            createTeamLogoCropper.destroy();
            createTeamLogoCropper = null;
        }
        if (createTeamBannerCropper) {
            createTeamBannerCropper.destroy();
            createTeamBannerCropper = null;
        }
        currentCreateTeamUploadType = '';
    }

    function cropAndSaveCreateTeam() {
        const cropper = currentCreateTeamUploadType === 'logo' ? createTeamLogoCropper : createTeamBannerCropper;
        if (!cropper) return;

        // Get cropped canvas
        const canvas = cropper.getCroppedCanvas({
            width: currentCreateTeamUploadType === 'logo' ? 300 : 800,
            height: currentCreateTeamUploadType === 'logo' ? 300 : 450,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Convert to blob and store for form submission
        canvas.toBlob(function(blob) {
            if (currentCreateTeamUploadType === 'logo') {
                createTeamLogoFile = blob;
            } else {
                createTeamBannerFile = blob;
            }
            
            // Update the UI to show the cropped image
            const uploadArea = document.querySelector(`#create-team-${currentCreateTeamUploadType}-upload`).parentElement;
            uploadArea.innerHTML = `
                <div class="text-center">
                    <div class="inline-block p-2 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="mt-2 text-sm text-green-600 font-medium">${currentCreateTeamUploadType === 'logo' ? 'Logo' : 'Banner'} ready for upload</p>
                    <button type="button" onclick="removeCreateTeamImage('${currentCreateTeamUploadType}')" class="text-red-600 text-xs hover:text-red-800 mt-1">
                        Remove
                    </button>
                </div>
            `;
            
            closeCreateTeamCropperModal();
        }, 'image/jpeg', 0.8);
    }

    function removeCreateTeamImage(type) {
        if (type === 'logo') {
            createTeamLogoFile = null;
        } else {
            createTeamBannerFile = null;
        }
        
        // Reset the upload area
        const uploadArea = document.querySelector(`#create-team-${type}-upload`).parentElement;
        uploadArea.innerHTML = `
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <input type="file" id="create-team-${type}-upload" accept="image/*" class="hidden">
                <button type="button" onclick="document.getElementById('create-team-${type}-upload').click()" class="text-indigo-600 hover:text-indigo-800">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">Click to upload ${type}</p>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to ${type === 'logo' ? '2MB' : '5MB'} (${type === 'logo' ? '1:1 ratio' : 'wide format'} recommended)</p>
                </button>
            </div>
        `;
        
        // Re-attach event listener
        document.getElementById(`create-team-${type}-upload`).addEventListener('change', function(e) {
            handleCreateTeamImageUpload(e, type);
        });
    }

    // Modify form submission to include cropped images
    document.querySelector('form').addEventListener('submit', function(e) {
        // Create a new FormData object
        const formData = new FormData(this);
        
        // Add cropped images if they exist
        if (createTeamLogoFile) {
            formData.append('logo', createTeamLogoFile, 'logo.jpg');
        }
        
        if (createTeamBannerFile) {
            formData.append('banner', createTeamBannerFile, 'banner.jpg');
        }
        
        // Prevent default form submission
        e.preventDefault();
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Creating Team...';
        submitBtn.disabled = true;
        
        // Submit via fetch
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            throw new Error('Network response was not ok');
        })
        .then(html => {
            // If successful, redirect to teams index
            window.location.href = '{{ route("teams.index") }}';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to create team. Please try again.');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
</script>

<!-- Include Cropper.js CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
@endsection
