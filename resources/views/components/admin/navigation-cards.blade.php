<div class="bg-white rounded-2xl shadow-lg p-6 mb-8 animate-fadeInUp">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 text-gray-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
        </svg>
        Admin Management
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <a href="{{ route('admin.organizer-requests.index') }}" 
           class="flex items-center p-4 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-colors {{ request()->routeIs('admin.organizer-requests.*') ? 'ring-2 ring-red-500' : '' }}">
            <div class="bg-red-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-red-900">Organizer Requests</h4>
                <p class="text-sm text-red-700">Manage organizer applications</p>
            </div>
        </a>

        <a href="{{ route('admin.locations.index') }}" 
           class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors {{ request()->routeIs('admin.locations.*') ? 'ring-2 ring-blue-500' : '' }}">
            <div class="bg-blue-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-blue-900">Location Management</h4>
                <p class="text-sm text-blue-700">Manage states, districts & local bodies</p>
            </div>
        </a>

        <a href="{{ route('admin.grounds.index') }}" 
           class="flex items-center p-4 bg-green-50 border border-green-200 rounded-xl hover:bg-green-100 transition-colors {{ request()->routeIs('admin.grounds.*') ? 'ring-2 ring-green-500' : '' }}">
            <div class="bg-green-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h4a2 2 0 012 2v2H6v-2z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-green-900">Ground Management</h4>
                <p class="text-sm text-green-700">Manage sports grounds & venues</p>
            </div>
        </a>

        <a href="{{ route('admin.players.index') }}" 
           class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-xl hover:bg-purple-100 transition-colors {{ request()->routeIs('admin.players.*') ? 'ring-2 ring-purple-500' : '' }}">
            <div class="bg-purple-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-purple-900">Users Management</h4>
                <p class="text-sm text-purple-700">Manage all users & reset PINs</p>
            </div>
        </a>

        <a href="{{ route('admin.analytics.index') }}" 
           class="flex items-center p-4 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition-colors {{ request()->routeIs('admin.analytics.*') ? 'ring-2 ring-indigo-500' : '' }}">
            <div class="bg-indigo-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-indigo-900">Analytics Dashboard</h4>
                <p class="text-sm text-indigo-700">View platform statistics & reports</p>
            </div>
        </a>

        <a href="{{ route('admin.admin-users.index') }}" 
           class="flex items-center p-4 bg-orange-50 border border-orange-200 rounded-xl hover:bg-orange-100 transition-colors {{ request()->routeIs('admin.admin-users.*') ? 'ring-2 ring-orange-500' : '' }}">
            <div class="bg-orange-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-orange-900">Admin Users</h4>
                <p class="text-sm text-orange-700">Manage admin accounts</p>
            </div>
        </a>

        <a href="{{ route('admin.auction-panel.index') }}" 
           class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-xl hover:bg-yellow-100 transition-colors {{ request()->routeIs('admin.auction-panel.*') ? 'ring-2 ring-yellow-500' : '' }}">
            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-yellow-900">Auction Panel</h4>
                <p class="text-sm text-yellow-700">Manage auction access requests</p>
            </div>
        </a>

        <a href="{{ route('admin.auctioneers.index') }}" 
           class="flex items-center p-4 bg-pink-50 border border-pink-200 rounded-xl hover:bg-pink-100 transition-colors {{ request()->routeIs('admin.auctioneers.*') ? 'ring-2 ring-pink-500' : '' }}">
            <div class="bg-pink-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-pink-900">Auctioneers</h4>
                <p class="text-sm text-pink-700">Manage league auctioneers</p>
            </div>
        </a>

        <a href="{{ route('admin.leagues.index') }}" 
           class="flex items-center p-4 bg-teal-50 border border-teal-200 rounded-xl hover:bg-teal-100 transition-colors {{ request()->routeIs('admin.leagues.*') ? 'ring-2 ring-teal-500' : '' }}">
            <div class="bg-teal-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM8 15a1 1 0 100-2 1 1 0 000 2zm4 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-teal-900">Leagues</h4>
                <p class="text-sm text-teal-700">Manage all leagues</p>
            </div>
        </a>

        <a href="{{ route('admin.league-players.index') }}" 
           class="flex items-center p-4 bg-cyan-50 border border-cyan-200 rounded-xl hover:bg-cyan-100 transition-colors {{ request()->routeIs('admin.league-players.*') ? 'ring-2 ring-cyan-500' : '' }}">
            <div class="bg-cyan-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-cyan-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-cyan-900">League Players</h4>
                <p class="text-sm text-cyan-700">Manage league registrations</p>
            </div>
        </a>

        <a href="{{ route('admin.teams.index') }}" 
           class="flex items-center p-4 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition-colors {{ request()->routeIs('admin.teams.*') ? 'ring-2 ring-emerald-500' : '' }}">
            <div class="bg-emerald-100 rounded-full p-2 mr-3">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-emerald-900">Teams</h4>
                <p class="text-sm text-emerald-700">Manage all teams</p>
            </div>
        </a>
    </div>
</div>
