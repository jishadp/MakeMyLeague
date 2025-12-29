<div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4">
    <!-- Mobile Layout -->
    <div class="sm:hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-gray-500">Match {{ $loop->iteration }}</span>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                {{ $fixture->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst(str_replace('_', ' ', $fixture->status)) }}
            </span>
        </div>

        <div class="mb-2">
            <select class="w-full border border-gray-300 rounded px-2 py-1 text-xs bg-gray-50"
                    onchange="updateFixture('{{ $fixture->slug }}', 'match_type', this.value, this)">
                <option value="group_stage" {{ $fixture->match_type === 'group_stage' ? 'selected' : '' }}>League Match</option>
                <option value="qualifier" {{ $fixture->match_type === 'qualifier' ? 'selected' : '' }}>Qualifier</option>
                <option value="eliminator" {{ $fixture->match_type === 'eliminator' ? 'selected' : '' }}>Eliminator</option>
                <option value="quarter_final" {{ $fixture->match_type === 'quarter_final' ? 'selected' : '' }}>Quarter Final</option>
                <option value="semi_final" {{ $fixture->match_type === 'semi_final' ? 'selected' : '' }}>Semi Final</option>
                <option value="final" {{ $fixture->match_type === 'final' ? 'selected' : '' }}>Final</option>
            </select>
        </div>
        
        <div class="space-y-2 mb-4">
            <div class="flex items-center justify-between">
                <span class="font-medium text-gray-900">{{ $fixture->homeTeam->team->name }}</span>
                <span class="text-sm text-gray-600">Home</span>
            </div>
            <div class="text-center text-xs text-gray-500">VS</div>
            <div class="flex items-center justify-between">
                <span class="font-medium text-gray-900">{{ $fixture->awayTeam->team->name }}</span>
                <span class="text-sm text-gray-600">Away</span>
            </div>
        </div>
        
        <!-- Mobile Schedule Form -->
        <div class="grid grid-cols-2 gap-2">
            <input type="date" 
                   value="{{ $fixture->match_date ? $fixture->match_date->format('Y-m-d') : '' }}"
                   class="border border-gray-300 rounded px-2 py-1 text-xs"
                   onchange="updateFixture('{{ $fixture->slug }}', 'match_date', this.value)">
            <input type="time" 
                   value="{{ $fixture->match_time ? $fixture->match_time->format('H:i') : '' }}"
                   class="border border-gray-300 rounded px-2 py-1 text-xs"
                   onchange="updateFixture('{{ $fixture->slug }}', 'match_time', this.value)">
            <input type="text" 
                   placeholder="Venue"
                   value="{{ $fixture->venue ?? '' }}"
                   class="border border-gray-300 rounded px-2 py-1 text-xs col-span-2"
                   onchange="updateFixture('{{ $fixture->slug }}', 'venue', this.value)">
        </div>
    </div>

    <!-- Desktop Layout -->
    <div class="hidden sm:block">
        <div class="grid grid-cols-12 gap-4 items-center">
            <!-- Teams -->
            <div class="col-span-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-medium text-gray-900">{{ $fixture->homeTeam->team->name }}</span>
                    <span class="text-sm text-gray-500">vs</span>
                    <span class="font-medium text-gray-900">{{ $fixture->awayTeam->team->name }}</span>
                </div>
                <select class="w-full border border-gray-200 rounded px-2 py-1 text-xs bg-gray-50 text-gray-600"
                        onchange="updateFixture('{{ $fixture->slug }}', 'match_type', this.value, this)">
                        <option value="group_stage" {{ $fixture->match_type === 'group_stage' ? 'selected' : '' }}>League Match</option>
                        <option value="qualifier" {{ $fixture->match_type === 'qualifier' ? 'selected' : '' }}>Qualifier</option>
                        <option value="eliminator" {{ $fixture->match_type === 'eliminator' ? 'selected' : '' }}>Eliminator</option>
                        <option value="quarter_final" {{ $fixture->match_type === 'quarter_final' ? 'selected' : '' }}>Quarter Final</option>
                        <option value="semi_final" {{ $fixture->match_type === 'semi_final' ? 'selected' : '' }}>Semi Final</option>
                        <option value="final" {{ $fixture->match_type === 'final' ? 'selected' : '' }}>Final</option>
                </select>
            </div>
            
            <!-- Date -->
            <div class="col-span-2">
                <input type="date" 
                       value="{{ $fixture->match_date ? $fixture->match_date->format('Y-m-d') : '' }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                       onchange="updateFixture('{{ $fixture->slug }}', 'match_date', this.value)">
            </div>
            
            <!-- Time -->
            <div class="col-span-2">
                <input type="time" 
                       value="{{ $fixture->match_time ? $fixture->match_time->format('H:i') : '' }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                       onchange="updateFixture('{{ $fixture->slug }}', 'match_time', this.value)">
            </div>
            
            <!-- Venue -->
            <div class="col-span-3">
                <input type="text" 
                       placeholder="Venue"
                       value="{{ $fixture->venue ?? '' }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                       onchange="updateFixture('{{ $fixture->slug }}', 'venue', this.value)">
            </div>
            
            <!-- Status -->
            <div class="col-span-1">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                    {{ $fixture->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $fixture->status)) }}
                </span>
            </div>
        </div>
    </div>
</div>
