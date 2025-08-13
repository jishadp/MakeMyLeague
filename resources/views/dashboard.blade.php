@extends('layouts.app')

@section('title', 'League Manager - Dashboard')

@section('content')
    @include('partials.hero')
    
    @include('partials.registration-cards')
    
    @include('partials.features')

    <div class="fixed bottom-4 right-4 p-4 bg-white rounded-full shadow-lg">
        <a href="{{ route('logout')}}" class="text-gray-700 hover:text-red-600 transition-colors flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Logout</span>
        </a>
    </div>
@endsection

@section('scripts')
<script>
    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // Adjust for header height
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add scroll animations
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements with specific classes
    document.querySelectorAll('.bg-white.rounded-xl, .bg-white.p-6').forEach(el => {
        el.classList.add('opacity-0');
        observer.observe(el);
    });
</script>
@endsection
