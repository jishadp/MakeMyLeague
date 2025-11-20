@props([
    'show' => false,
    'isSold' => false,
    'isUnsold' => false,
])

@once
    <style>
        .auction-result-overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .auction-result-overlay__wrapper {
            position: absolute;
            inset-inline: 0;
            bottom: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            height: 9rem;
            pointer-events: none;
            z-index: 30;
        }

        .auction-badge {
            position: relative;
            transform: translateY(12px) scale(0.7);
            animation: badgePop 0.9s ease-out forwards;
            z-index: 50;
        }

        .auction-badge img {
            width: 110px;
            max-width: 30vw;
            filter:
                drop-shadow(0 0 12px rgba(0, 255, 180, 0.65))
                drop-shadow(0 10px 35px rgba(0, 0, 0, 0.35));
            animation: badgeFloat 3s ease-in-out infinite;
        }

        @keyframes badgePop {
            0% { opacity: 0; transform: translateY(20px) scale(0.4); }
            60% { opacity: 1; transform: translateY(-6px) scale(1.08); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes badgeFloat {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(-6px) rotate(2deg); }
        }

        .particle-burst {
            position: absolute;
            inset-inline: 0;
            bottom: 0.5rem;
            width: 160px;
            height: 160px;
            pointer-events: none;
            z-index: 40;
            transform: translateY(8px);
        }

        .particle-burst span {
            position: absolute;
            width: 7px;
            height: 7px;
            background: rgba(0, 255, 200, 0.9);
            border-radius: 50%;
            animation: burst 0.9s ease-out forwards;
        }

        @keyframes burst {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(var(--x), var(--y)) scale(0); opacity: 0; }
        }
    </style>
@endonce

@if($show && ($isSold || $isUnsold))
    <div class="auction-result-overlay">
        <div class="auction-result-overlay__wrapper">
            <div class="auction-badge">
                <img src="{{ asset('images/auction/' . ($isUnsold ? 'unsold' : 'sold') . '.png') }}"
                     alt="{{ $isUnsold ? 'Unsold' : 'Sold' }} animation">
            </div>
            <div class="particle-burst">
                @for($i = 0; $i < 18; $i++)
                    <span style="--x: {{ rand(-70, 70) }}px; --y: {{ rand(-70, 70) }}px;"></span>
                @endfor
            </div>
        </div>
    </div>
@endif
