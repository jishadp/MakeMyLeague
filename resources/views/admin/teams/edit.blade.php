@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.teams.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Teams</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Team</h1>

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('admin.teams.update', $team) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Team Name</label>
                        <input type="text" name="name" value="{{ old('name', $team->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Team</button>
                    </div>
                </div>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Team Images</h2>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Logo</h3>
                        @if($team->logo)
                            <img src="{{ asset('storage/' . $team->logo) }}" class="w-32 h-32 object-cover rounded-lg mb-2">
                            <form action="{{ route('admin.teams.remove-logo', $team) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900">Remove Logo</button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500 mb-2">No logo uploaded</p>
                        @endif
                        <form action="{{ route('admin.teams.upload-logo', $team) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                            @csrf
                            <input type="file" name="logo" accept="image/*" class="text-sm">
                            <button type="submit" class="mt-2 px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">Upload Logo</button>
                        </form>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Banner</h3>
                        @if($team->banner)
                            <img src="{{ asset('storage/' . $team->banner) }}" class="w-full h-32 object-cover rounded-lg mb-2">
                            <form action="{{ route('admin.teams.remove-banner', $team) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900">Remove Banner</button>
                            </form>
                        @else
                            <p class="text-sm text-gray-500 mb-2">No banner uploaded</p>
                        @endif
                        <form action="{{ route('admin.teams.upload-banner', $team) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                            @csrf
                            <input type="file" name="banner" accept="image/*" class="text-sm">
                            <button type="submit" class="mt-2 px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">Upload Banner</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
