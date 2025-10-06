@extends('layouts.app')

@section('title', 'Create Player | '. config('app.name'))

@section('content')
<div class="py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 animate-fadeIn">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('players.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Players
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create Player</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                @if(request('league_slug'))
                    Create Player for League
                @else
                    Create Player
                @endif
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                @if(request('league_slug'))
                    Add a new cricket player that will be automatically added to the league
                @else
                    Add a new cricket player to the system
                @endif
            </p>
        </div>
        
        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 animate-fadeInUp">
            <div class="p-8">
                @if ($errors->any())
                <div class="mb-6 bg-red-50 text-red-600 p-4 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('players.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(request('league_slug'))
                        <input type="hidden" name="league_slug" value="{{ request('league_slug') }}">
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                        </div>

                        <!-- Mobile -->
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                            <input id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                                   required
                                   placeholder="Enter your mobile number">
                            <!-- Hidden country code field -->
                            <input type="hidden" name="country_code" value="+91">
                        </div>

                        <!-- PIN -->
                        <div>
                            <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN (4 digits) <span class="text-red-500">*</span></label>
                            <input type="password" name="pin" id="pin" value="{{ old('pin') }}" required
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                                   minlength="4" maxlength="4">
                        </div>

                        <!-- District -->
                        <div>
                            <label for="district_id" class="block text-sm font-medium text-gray-700 mb-2">District <span class="text-red-500">*</span></label>
                            <select id="district_id" name="district_id" 
                                    class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
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
                            <label for="local_body_id" class="block text-sm font-medium text-gray-700 mb-2">Location <span class="text-red-500">*</span></label>
                            <select id="local_body_id" name="local_body_id" 
                                    class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4"
                                    required disabled>
                                <option value="">First select a district</option>
                            </select>
                        </div>

                        <!-- Player Role -->
                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">Player Position <span class="text-red-500">*</span></label>
                            <select name="position_id" id="position_id" required
                                    class="select2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 text-base py-3 px-4">
                                <option value="">Select Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Profile Photo -->
                    <div class="mt-6">
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo (Optional)</label>
                        <div class="flex items-center space-x-4">
                            <input type="file" name="photo" id="photo" accept="image/*" style="display: none;">
                            <button type="button" id="selectPhotoBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Select Photo
                            </button>
                            <div id="photoPreview" class="hidden">
                                <img id="previewImg" src="" alt="Preview" class="w-16 h-16 object-cover rounded-lg border-2 border-gray-300">
                                <button type="button" id="removePhotoBtn" class="ml-2 text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Accepted formats: JPEG, PNG, JPG. Max size: 2MB. Image will be cropped and resized to 300x300 pixels.</p>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-between">
                        <a href="{{ route('players.index') }}" 
                           class="underline text-sm text-gray-600 hover:text-gray-900 mb-4 sm:mb-0">
                            Back to Players
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Create Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Crop Modal -->
<div id="cropModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Crop Your Photo</h3>
                    <button type="button" id="closeCropModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mb-4">
                    <div id="cropContainer" class="w-full h-96 bg-gray-100 rounded-lg overflow-hidden">
                        <img id="cropImage" src="" alt="Crop" class="max-w-full max-h-full">
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelCrop" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" id="cropPhoto" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Crop & Save
                    </button>
                </div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css">
<script src="{{ asset('js/countries.js') }}?v={{ time() }}"></script>
<script>
    let cropper;
    let selectedFile;

    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });

        // Initialize cascade select functionality
        initializeCascadeSelect();

        // Photo selection and cropping
        initializePhotoCropping();
    });

    function initializePhotoCropping() {
        const selectPhotoBtn = document.getElementById('selectPhotoBtn');
        const photoInput = document.getElementById('photo');
        const cropModal = document.getElementById('cropModal');
        const cropImage = document.getElementById('cropImage');
        const cropPhotoBtn = document.getElementById('cropPhoto');
        const cancelCropBtn = document.getElementById('cancelCrop');
        const closeCropModal = document.getElementById('closeCropModal');
        const photoPreview = document.getElementById('photoPreview');
        const previewImg = document.getElementById('previewImg');
        const removePhotoBtn = document.getElementById('removePhotoBtn');

        // Select photo button click
        selectPhotoBtn.addEventListener('click', () => {
            photoInput.click();
        });

        // File input change
        photoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    return;
                }

                // Check file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    return;
                }

                selectedFile = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    cropImage.src = e.target.result;
                    cropModal.classList.remove('hidden');
                    initializeCropper();
                };
                reader.readAsDataURL(file);
            }
        });

        // Initialize cropper
        function initializeCropper() {
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(cropImage, {
                aspectRatio: 1, // Square crop
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

        // Crop photo button
        cropPhotoBtn.addEventListener('click', () => {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 300,
                    height: 300,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                // Convert to blob and check size
                canvas.toBlob((blob) => {
                    // Check if blob is under 512KB
                    if (blob.size > 512 * 1024) {
                        // Reduce quality until under 512KB
                        let quality = 0.9;
                        const reduceQuality = () => {
                            canvas.toBlob((newBlob) => {
                                if (newBlob.size > 512 * 1024 && quality > 0.1) {
                                    quality -= 0.1;
                                    reduceQuality();
                                } else {
                                    // Create new file from blob
                                    const croppedFile = new File([newBlob], selectedFile.name, {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    });

                                    // Update file input
                                    const dt = new DataTransfer();
                                    dt.items.add(croppedFile);
                                    photoInput.files = dt.files;

                                    // Show preview
                                    previewImg.src = URL.createObjectURL(newBlob);
                                    photoPreview.classList.remove('hidden');
                                    selectPhotoBtn.textContent = 'Change Photo';

                                    // Close modal
                                    cropModal.classList.add('hidden');
                                    if (cropper) {
                                        cropper.destroy();
                                        cropper = null;
                                    }
                                }
                            }, 'image/jpeg', quality);
                        };
                        reduceQuality();
                    } else {
                        // File is already under 512KB
                        const croppedFile = new File([blob], selectedFile.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });

                        // Update file input
                        const dt = new DataTransfer();
                        dt.items.add(croppedFile);
                        photoInput.files = dt.files;

                        // Show preview
                        previewImg.src = URL.createObjectURL(blob);
                        photoPreview.classList.remove('hidden');
                        selectPhotoBtn.textContent = 'Change Photo';

                        // Close modal
                        cropModal.classList.add('hidden');
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                    }
                }, 'image/jpeg', 0.9);
            }
        });

        // Cancel crop
        cancelCropBtn.addEventListener('click', () => {
            cropModal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            photoInput.value = '';
        });

        // Close modal
        closeCropModal.addEventListener('click', () => {
            cropModal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            photoInput.value = '';
        });

        // Remove photo
        removePhotoBtn.addEventListener('click', () => {
            photoInput.value = '';
            photoPreview.classList.add('hidden');
            selectPhotoBtn.textContent = 'Select Photo';
            if (previewImg.src) {
                URL.revokeObjectURL(previewImg.src);
                previewImg.src = '';
            }
        });

        // Close modal on backdrop click
        cropModal.addEventListener('click', (e) => {
            if (e.target === cropModal) {
                cropModal.classList.add('hidden');
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                photoInput.value = '';
            }
        });
    }

    function initializeCascadeSelect() {
        const districtSelect = document.getElementById('district_id');
        const localBodySelect = document.getElementById('local_body_id');
        
        // Local bodies data from PHP
        const localBodies = @json($localBodies);
        
        // Handle district selection change (using Select2 event)
        $(districtSelect).on('change', function() {
            const selectedDistrictId = this.value;
            
            // Clear local body options
            localBodySelect.innerHTML = '<option value="">Select a location</option>';
            
            if (selectedDistrictId) {
                // Filter local bodies by selected district
                const filteredLocalBodies = localBodies.filter(localBody => 
                    localBody.district_id == selectedDistrictId
                );
                
                // Add filtered local bodies to select
                filteredLocalBodies.forEach(localBody => {
                    const option = document.createElement('option');
                    option.value = localBody.id;
                    option.textContent = localBody.name;
                    localBodySelect.appendChild(option);
                });
                
                // Enable local body select
                localBodySelect.disabled = false;
            } else {
                // Disable local body select if no district selected
                localBodySelect.disabled = true;
            }
            
            // Refresh Select2 for local body
            $(localBodySelect).select2('destroy').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select a location',
                allowClear: true
            });
        });
        
        // Handle form validation errors - pre-select district and local body
        const oldDistrictId = '{{ old('district_id') }}';
        const oldLocalBodyId = '{{ old('local_body_id') }}';
        
        if (oldDistrictId) {
            $(districtSelect).val(oldDistrictId).trigger('change');
            
            // Wait for local bodies to load, then select the old value
            setTimeout(() => {
                if (oldLocalBodyId) {
                    $(localBodySelect).val(oldLocalBodyId).trigger('change');
                }
            }, 100);
        }
    }
</script>
@endsection
