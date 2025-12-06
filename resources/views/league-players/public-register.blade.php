@extends('layouts.app')

@section('title', 'Player Registration - ' . $league->name)

@php
    $shareUrl = route('league-players.public-register', $league);
@endphp

@section('content')
<section class="min-h-screen bg-gradient-to-b from-white via-indigo-50 to-blue-50 text-slate-900">
    <div class="max-w-3xl mx-auto px-4 py-10 space-y-6">
        <div class="bg-white border border-indigo-100 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">Player Registration</span>
                <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Public player sign-up</p>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">{{ $league->name }}</h1>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-700 mt-3">
                <span class="px-3 py-1 rounded-full border border-indigo-100 bg-indigo-50 text-indigo-700">
                    Game: {{ $league->game->name ?? 'Any sport' }}
                </span>
                @if($league->localBody)
                    <span class="px-3 py-1 rounded-full border border-indigo-100 bg-indigo-50 text-indigo-700">
                        {{ $league->localBody->name }} {{ $league->localBody->district?->name ? '- ' . $league->localBody->district->name : '' }}
                    </span>
                @endif
                <span class="px-3 py-1 rounded-full border border-blue-100 bg-blue-50 text-blue-700">
                    Slots available: {{ $slotsRemaining }}
                </span>
                <span class="px-3 py-1 rounded-full border border-indigo-100 bg-indigo-50 text-indigo-700">
                    Registered (incl. pending): {{ $currentPlayerCount }} / {{ $maxPlayers }}
                </span>
                <span class="px-3 py-1 rounded-full border border-amber-100 bg-amber-50 text-amber-700">
                    Registration fee: ₹{{ number_format($league->player_reg_fee ?? 0) }}
                </span>
            </div>
            <div class="flex flex-wrap gap-3 mt-4">
                <a href="https://wa.me/919400960223?text={{ urlencode('Hi, I would like to complete player registration payment for ' . $league->name) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold text-white transition-colors">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    Payment on WhatsApp
                </a>
                <a href="https://wa.me/?text={{ urlencode('Register for ' . $league->name . ' here: ' . $shareUrl) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 rounded-lg text-sm font-semibold text-white transition-colors">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    Share to WhatsApp
                </a>
            </div>
            @if(!$registrationOpen)
                <div class="mt-4 text-amber-200 bg-amber-500/10 border border-amber-400/40 rounded-xl px-4 py-3">
                    Registration is currently closed for this league. You can still share the link and check back later.
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 text-slate-900">
            @if(session('success'))
                <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <p class="font-semibold mb-2">Please fix the highlighted fields:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('league-players.public-register.store', $league) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="country_code" value="{{ old('country_code', '+91') }}">

                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-slate-800">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Enter your full name" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" @if(!$registrationOpen) disabled @endif>
                </div>

                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-slate-800">Mobile Number</label>
                    <div class="flex rounded-xl border border-slate-200 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-200">
                        <span class="px-3 flex items-center text-slate-500 border-r border-slate-200">+91</span>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" name="mobile" value="{{ old('mobile') }}" required placeholder="10 digit mobile number" class="flex-1 px-4 py-3 rounded-r-xl focus:outline-none" @if(!$registrationOpen) disabled @endif>
                    </div>
                    <p class="text-xs text-slate-500">We keep your mobile unique so organizers can reach you.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-slate-800">PIN</label>
                    <input type="password" name="pin" minlength="4" maxlength="6" required placeholder="Choose a 4-6 digit PIN" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" @if(!$registrationOpen) disabled @endif>
                    <p class="text-xs text-slate-500">Use this PIN for quick login after registration.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-slate-800">Game Position</label>
                    <select name="position_id" required class="w-full rounded-xl border border-slate-200 px-4 py-3 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" @if(!$registrationOpen) disabled @endif>
                        <option value="">Select your position</option>
                        @foreach($gamePositions as $position)
                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                    @if($gamePositions->isEmpty())
                        <p class="text-xs text-amber-600">No roles are configured for this game yet. Please contact the organizer.</p>
                    @endif
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-slate-800">Profile Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center">
                            <img id="photo-preview" src="{{ asset('images/defaultplayer.jpeg') }}" alt="Profile preview" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" id="photo-input" accept="image/*" class="hidden" onchange="showCropModal(this)" @if(!$registrationOpen) disabled @endif>
                            <button type="button" onclick="document.getElementById('photo-input').click()" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-800 hover:bg-slate-50 font-semibold" @if(!$registrationOpen) disabled @endif>
                                Upload & Crop
                            </button>
                            <p class="text-xs text-slate-500 mt-1">Square photo works best. Optional but helps teams spot you.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <span class="block text-sm font-semibold text-slate-800">Payment</span>
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 cursor-pointer hover:border-indigo-500">
                        <input type="radio" name="payment_status" value="pay_later" class="h-4 w-4 text-indigo-600 border-slate-300" {{ old('payment_status', 'pay_later') === 'pay_later' ? 'checked' : '' }} @if(!$registrationOpen) disabled @endif>
                        <div>
                            <p class="font-semibold text-slate-900">Pay later</p>
                            <p class="text-xs text-slate-500">Reserve a slot now, settle the fee with organizers.</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 cursor-pointer hover:border-indigo-500">
                        <input type="radio" name="payment_status" value="paid" class="h-4 w-4 text-indigo-600 border-slate-300" {{ old('payment_status') === 'paid' ? 'checked' : '' }} @if(!$registrationOpen) disabled @endif>
                        <div>
                            <p class="font-semibold text-slate-900">Paid</p>
                            <p class="text-xs text-slate-500">I have already paid the registration fee.</p>
                        </div>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white font-semibold px-4 py-3 shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed" @if(!$registrationOpen) disabled @endif>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Submit registration
                    </button>
                    <p class="text-xs text-slate-500 mt-2">You will be added as pending. Organizers approve before auction lists go live.</p>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Crop Modal -->
<div id="crop-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900">Crop your photo</h3>
            <button type="button" onclick="closeCropModal(true)" class="text-slate-500 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="crop-container" class="w-full h-72 bg-slate-100 rounded-xl overflow-hidden border border-slate-200"></div>
        <div class="flex gap-3 mt-5">
            <button type="button" onclick="closeCropModal(true)" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">Cancel</button>
            <button type="button" onclick="applyCrop()" class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">Use photo</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" referrerpolicy="no-referrer" />

<script>
let cropperInstance;
let selectedFile;

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
                autoCropArea: 1,
                background: false,
                responsive: true
            });

            document.getElementById('crop-modal').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function closeCropModal(resetInput = false) {
    document.getElementById('crop-modal').classList.add('hidden');
    if (cropperInstance) {
        cropperInstance.destroy();
        cropperInstance = null;
    }
    selectedFile = null;
    if (resetInput) {
        document.getElementById('photo-input').value = '';
    }
}

function applyCrop() {
    if (!cropperInstance || !selectedFile) return;

    const canvas = cropperInstance.getCroppedCanvas({
        width: 500,
        height: 500,
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
