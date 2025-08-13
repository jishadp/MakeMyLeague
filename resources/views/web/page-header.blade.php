<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between">
        
        <!-- Page Title & Description -->
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 leading-tight">
                @yield('page-title')
            </h2>
            
            @hasSection('page-description')
                <p class="mt-1 text-sm sm:text-base text-gray-500 leading-relaxed">
                    @yield('page-description')
                </p>
            @endif
        </div>
        
        <!-- Page Actions -->
        @hasSection('page-actions')
            <div class="mt-4 flex flex-wrap gap-2 md:mt-0 md:ml-4">
                @yield('page-actions')
            </div>
        @endif
    </div>
</div>
