@extends('layouts.app')

@section('title', 'Player Registration - ' . $league->name)

@php
    $shareUrl = route('league-players.public-register', $league);
    $playerFill = $maxPlayers > 0 ? min(100, ($currentPlayerCount / $maxPlayers) * 100) : 0;
@endphp

@section('content')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .stats-scroll::-webkit-scrollbar { display: none; }
    .stats-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<section class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/50 to-blue-100/50 text-slate-900 pb-20">
    <div class="fixed top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none"></div>
    <div class="fixed bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none"></div>

    <div class="relative max-w-3xl mx-auto px-4 py-8 sm:py-12 space-y-6">
        
        <div class="bg-white/80 backdrop-blur-xl border border-white/50 rounded-3xl p-6 sm:p-8 shadow-xl shadow-indigo-900/5 animate-fade-in-up">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-slate-50 shadow-md shadow-blue-500/20">
                            Player Registration
                        </span>
                    </div>
                    
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                        {{ $league->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-2 text-sm font-medium text-slate-600">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                            <i class="fa-solid fa-trophy text-xs"></i>
                            <span>{{ $league->game->name ?? 'Any sport' }}</span>
                        </div>
                        @if($league->localBody)
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                <i class="fa-solid fa-location-dot text-xs"></i>
                                <span>{{ $league->localBody->name }} {{ $league->localBody->district?->name ? '- ' . $league->localBody->district->name : '' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="w-full md:w-auto flex flex-col sm:flex-row md:flex-col gap-3">
                    <a href="https://wa.me/919400960223?text={{ urlencode('Hi, I would like to complete player registration payment for ' . $league->name) }}" 
                       target="_blank" rel="noopener" 
                       class="group relative overflow-hidden inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#25D366] hover:bg-[#20bd5a] rounded-xl text-sm font-bold text-black transition-all duration-300 shadow-lg shadow-green-500/20 hover:shadow-green-500/40 hover:-translate-y-0.5">
                        <i class="fa-brands fa-whatsapp text-lg group-hover:scale-110 transition-transform"></i>
                        <span>Payment</span>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode('Register for ' . $league->name . ' here: ' . $shareUrl) }}" 
                       target="_blank" rel="noopener" 
                       class="group inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-indigo-50 rounded-xl text-sm font-bold text-slate-900 transition-all duration-300 shadow-md shadow-indigo-100 border border-indigo-100">
                        <i class="fa-brands fa-whatsapp text-lg text-indigo-600"></i>
                        <span>Share Reg Link</span>
                    </a>
                </div>
            </div>

            <div class="relative mt-8">
    <div class="grid grid-cols-3 gap-2">

        <!-- CARD 1 -->
        <div class="group relative rounded-xl border border-blue-100 bg-white p-3 min-w-0">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-white/80 text-blue-600 shadow-sm">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
                <div>
                    <p class="text-[9px] uppercase tracking-wide font-bold text-blue-600">Slots</p>
                    <p class="text-xl font-black text-slate-900 leading-none">{{ $slotsRemaining }}</p>
                </div>
            </div>
            <p class="mt-1 text-[9px] text-slate-500 flex items-center gap-1">
                <span class="h-1 w-1 rounded-full bg-emerald-400 animate-pulse"></span> Live
            </p>
        </div>

        <!-- CARD 2 -->
        <div class="group relative rounded-xl border border-indigo-100 bg-white p-3 min-w-0">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-white/80 text-indigo-600 shadow-sm">
                    <i class="fa-solid fa-clipboard-list text-sm"></i>
                </div>
                <div>
                    <p class="text-[9px] uppercase tracking-wide font-bold text-indigo-600">Reg</p>
                    <p class="text-xl font-black text-slate-900 leading-none">
                        {{ $currentPlayerCount }} <span class="text-xs text-slate-400">/ {{ $maxPlayers }}</span>
                    </p>
                </div>
            </div>

            <div class="mt-1">
                <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-blue-500" style="width: {{ $playerFill }}%;"></div>
                </div>
            </div>
        </div>

        <!-- CARD 3 -->
        <div class="group relative rounded-xl border border-amber-100 bg-white p-3 min-w-0">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-white/80 text-amber-600 shadow-sm">
                    <i class="fa-solid fa-coins text-sm"></i>
                </div>
                <div>
                    <p class="text-[9px] uppercase tracking-wide font-bold text-amber-600">Fee</p>
                    <p class="text-xl font-black text-slate-900 leading-none">
                        ₹{{ number_format($league->player_reg_fee ?? 0) }}
                    </p>
                </div>
            </div>
            <p class="mt-1 text-[9px] text-slate-500">Per player</p>
        </div>

    </div>
            </div>

            @if(!$registrationOpen)
                <div class="mt-6 flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-2xl p-4 animate-pulse">
                    <i class="fa-solid fa-lock text-amber-500 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-amber-800 text-sm">Registration Closed</h4>
                        <p class="text-xs text-amber-700 mt-1">You can still share the link and check back later.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- FORM CARD -->
        <div class="bg-white/80 backdrop-blur-xl border border-white/50 rounded-3xl p-6 sm:p-8 shadow-xl shadow-indigo-900/5 animate-fade-in-up delay-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 flex items-center gap-3">
                    <div class="bg-green-100 p-2 rounded-full text-green-600"><i class="fa-solid fa-check"></i></div>
                    <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                        <h4 class="font-bold text-red-800 text-sm">Action Required</h4>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-red-700 ml-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

<form action="{{ route('league-players.public-register.store', $league) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="country_code" value="{{ old('country_code', '+91') }}">

                <div class="space-y-1.5 group">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Full Name</label>
                    <div class="relative">
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Lionel Messi" 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 pl-11 text-slate-900 placeholder-slate-400 font-medium focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300" 
                            @if(!$registrationOpen) disabled @endif>
                        <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5 group">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Mobile Number</label>
                        <div class="flex rounded-xl border border-slate-200 bg-slate-50 overflow-hidden focus-within:border-indigo-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-indigo-500/10 transition-all duration-300">
                            <span class="px-4 py-3.5 bg-slate-100 text-slate-500 font-semibold border-r border-slate-200 text-sm flex items-center">+91</span>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" name="mobile" value="{{ old('mobile') }}" required placeholder="98765 43210" 
                                class="flex-1 px-4 py-3.5 bg-transparent border-none focus:ring-0 text-slate-900 font-medium placeholder-slate-400" 
                                @if(!$registrationOpen) disabled @endif>
                        </div>
                    </div>

                    <div class="space-y-1.5 group">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Secret PIN</label>
                        <div class="relative">
                            <input type="password" name="pin" minlength="4" maxlength="6" required placeholder="4 digits Only" 
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 pl-11 text-slate-900 placeholder-slate-400 font-medium focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300" 
                                @if(!$registrationOpen) disabled @endif>
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5 group">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Preferred Position</label>
                    <div class="relative">
                        <select name="position_id" required 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 font-medium focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300 cursor-pointer" 
                            @if(!$registrationOpen) disabled @endif>
                            <option value="">Select Position...</option>
                            @foreach($gamePositions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5 group">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Local Body</label>
                    <div class="relative">
                        <select name="local_body_id" required 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-slate-900 font-medium focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300 cursor-pointer" 
                            @if(!$registrationOpen) disabled @endif>
                            <option value="">Select Location...</option>
                            @foreach($localBodies as $localBody)
                                <option value="{{ $localBody->id }}" {{ old('local_body_id', $league->local_body_id) == $localBody->id ? 'selected' : '' }}>
                                    {{ $localBody->name }}{{ $localBody->district ? ' - ' . $localBody->district->name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-3 p-5 rounded-2xl border border-slate-200 bg-white shadow-sm hover:-translate-y-0.5 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center gap-5">
                        <div class="relative group/img">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-white shadow-md">
                                <img id="photo-preview" src="{{ asset('images/defaultplayer.jpeg') }}" alt="Profile preview" class="w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-110">
                            </div>
                            <div class="absolute inset-0 bg-black/20 rounded-2xl flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity">
                                <i class="fa-solid fa-camera text-slate-50 text-lg drop-shadow-md"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-slate-900">Profile Photo</label>
                            <p class="text-xs text-slate-500 mb-3">Upload a clear photo. Helps teams identify you.</p>
                            
                            <input type="file" name="photo" id="photo-input" accept="image/*" class="hidden" onchange="showCropModal(this)" @if(!$registrationOpen) disabled @endif>
                            <button type="button" onclick="document.getElementById('photo-input').click()" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-indigo-600 shadow-sm hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-700 transition-all"
                                @if(!$registrationOpen) disabled @endif>
                                <i class="fa-solid fa-upload"></i> Upload & Crop
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Payment Status</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/30 transition-all duration-200 group">
                            <input type="radio" name="payment_status" value="pay_later" class="peer h-4 w-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" {{ old('payment_status', 'pay_later') === 'pay_later' ? 'checked' : '' }} @if(!$registrationOpen) disabled @endif>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 text-sm group-hover:text-indigo-700">Pay Later</p>
                                <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Reserve slot, pay organizer later.</p>
                            </div>
                            <div class="absolute inset-0 border-2 border-indigo-500 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                        </label>
                        
                        <label class="relative flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/30 transition-all duration-200 group">
                            <input type="radio" name="payment_status" value="paid" class="peer h-4 w-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" {{ old('payment_status') === 'paid' ? 'checked' : '' }} @if(!$registrationOpen) disabled @endif>
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 text-sm group-hover:text-indigo-700">Already Paid</p>
                                <p class="text-[11px] text-slate-500 leading-tight mt-0.5">I have settled the registration fee.</p>
                            </div>
                            <div class="absolute inset-0 border-2 border-indigo-500 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                        </label>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" id="submitBtn"
    class="group relative w-full inline-flex items-center justify-center gap-2 rounded-xl 
           bg-gradient-to-r from-indigo-600 to-blue-600 text-black font-bold text-lg 
           px-6 py-4 shadow-xl shadow-indigo-500/30 
           hover:shadow-indigo-500/50 hover:-translate-y-0.5 
           active:translate-y-0 active:scale-[0.99] 
           transition-all duration-200 
           disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none"
    @if(!$registrationOpen) disabled @endif>

    <span class="z-10">Submit Registration</span>
    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform z-10"></i>

    <div class="absolute inset-0 bg-indigo-600 rounded-xl flex items-center justify-center hidden" id="btnLoader">
        <svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</button>
                    <p class="text-center text-xs text-slate-400 mt-4 font-medium">
                        By registering, you agree to league terms. <br>Approvals are subject to organizer review.
                    </p>
                </div>
            </form>
        </div>

</div> <!-- container end -->
</section>

<!-- CROP MODAL -->
<div id="crop-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4 z-50 hidden transition-opacity duration-300 opacity-0" role="dialog" aria-modal="true">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform scale-95 transition-transform duration-300" id="crop-modal-content">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-crop-simple text-indigo-500"></i> Adjust Photo
            </h3>
            <button type="button" onclick="closeCropModal(true)" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-red-100 hover:text-red-500 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div class="p-5 bg-slate-50">
            <div id="crop-container" class="w-full h-80 bg-slate-200 rounded-xl overflow-hidden border border-slate-300 shadow-inner relative"></div>
            <p class="text-center text-xs text-slate-400 mt-2">Drag to position. Scroll to zoom.</p>
        </div>

        <div class="p-5 border-t border-slate-100 flex gap-3 bg-white">
            <button type="button" onclick="closeCropModal(true)" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 hover:text-slate-900 transition-colors">
                Cancel
            </button>
            <button type="button" onclick="applyCrop()" class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 text-indigo-50 font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 transition-colors">
                Save Photo
            </button>
        </div>
    </div>
</div>

<!-- CROP SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" referrerpolicy="no-referrer" />

<script>
let cropperInstance;
let selectedFile;
const modal = document.getElementById('crop-modal');
const modalContent = document.getElementById('crop-modal-content');

// Submit animation
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const loader = document.getElementById('btnLoader');
    btn.disabled = true;
    loader.classList.remove('hidden');
});

function showCropModal(input) {
    if (input.files && input.files[0]) {
        selectedFile = input.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            const image = document.createElement('img');
            image.src = e.target.result;
            image.style.maxWidth = '100%';
            
            const container = document.getElementById('crop-container');
            container.innerHTML = '';
            container.appendChild(image);

            cropperInstance = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 0.8,
                background: false,
                responsive: true,
                guides: true,
            });

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function closeCropModal(resetInput = false) {
    modal.classList.add('opacity-0');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
        selectedFile = null;
        if (resetInput) {
            document.getElementById('photo-input').value = '';
        }
    }, 300);
}

function applyCrop() {
    if (!cropperInstance || !selectedFile) return;

    const canvas = cropperInstance.getCroppedCanvas({
        width: 600,
        height: 600,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });

    canvas.toBlob(function (blob) {
        const file = new File([blob], 'player-photo.jpg', { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);

        const photoInput = document.getElementById('photo-input');
        photoInput.files = dataTransfer.files;

        const preview = document.getElementById('photo-preview');
        preview.src = URL.createObjectURL(file);

        closeCropModal();
    }, 'image/jpeg', 0.9);
}
</script>

@endsection
