<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Store'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        void: '#050507',
                        surface: '#0A0A0F',
                        border: 'rgba(255,255,255,0.06)',
                        muted: 'rgba(255,255,255,0.5)',
                        accent: '#E8FF57',
                        'accent-dark': '#C4E044',
                        glow: 'rgba(232,255,87,0.15)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=swear@400,500,600&f[]=general-sans@400,500,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --void: #050507;
            --surface: #0A0A0F;
            --accent: #E8FF57;
            --glow: rgba(232,255,87,0.15);
        }
        * { -webkit-font-smoothing: antialiased; }
        body {
            font-family: 'General Sans', system-ui, sans-serif;
            background: var(--void);
            color: #fff;
            overflow-x: hidden;
        }
        .font-display { font-family: 'Swear', serif; }
        
        .bg-noise {
            position: fixed; inset: 0; pointer-events: none; z-index: 9999;
            opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }
        
        .bg-grid {
            background-image: 
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.6) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .text-accent-gradient {
            background: linear-gradient(135deg, var(--accent) 0%, #fff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .border-glow {
            box-shadow: 0 0 0 1px rgba(232,255,87,0.2), 0 0 40px rgba(232,255,87,0.05);
        }
        
        .btn-glow {
            background: var(--accent);
            color: #000;
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 100px;
            transition: all 0.3s cubic-bezier(0.32, 0.72, 0, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-glow::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.3), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .btn-glow:hover::before { opacity: 1; }
        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(232,255,87,0.3);
        }
        
        .btn-outline {
            border: 1px solid rgba(255,255,255,0.15);
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 100px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.3);
        }
        
        .product-card {
            position: relative;
            transition: all 0.5s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .product-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(232,255,87,0.1), transparent);
            opacity: 0;
            transition: opacity 0.5s;
        }
        .product-card:hover::after { opacity: 1; }
        .product-card:hover { transform: translateY(-12px); }
        
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }
        .orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(232,255,87,0.12), transparent 70%);
            top: -200px; left: -100px;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(147,112,219,0.1), transparent 70%);
            bottom: 20%; right: -100px;
        }
        .orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(232,255,87,0.08), transparent 70%);
            top: 50%; left: 50%;
        }
        
        .reveal {
            opacity: 0;
            transform: translateY(60px);
            transition: all 0.8s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .marquee-track {
            animation: marquee 40s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        .hover-lift {
            transition: all 0.4s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.03);
        }
        
        .big-text {
            font-size: clamp(4rem, 15vw, 14rem);
            line-height: 0.85;
            letter-spacing: -0.04em;
        }
        
        @media (max-width: 768px) {
            .big-text { font-size: clamp(3rem, 18vw, 6rem); }
        }
        
        .line-reveal {
            overflow: hidden;
        }
        .line-reveal span {
            display: block;
            transform: translateY(100%);
            transition: transform 0.8s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .line-reveal.visible span { transform: translateY(0); }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col">
    <div class="bg-noise"></div>
    
    @php
        $cartCount = 0;
        try {
            $sessionId = session('cart_session_id');
            if ($sessionId) {
                $cartCount = \App\Models\Cart::where('session_id', $sessionId)->sum('quantity');
            }
        } catch (\Exception $e) {}
    @endphp
    
    <nav class="fixed top-0 left-0 right-0 z-50 px-4 md:px-6 pt-4 md:pt-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between px-6 py-4 rounded-full border-glow bg-surface/50 backdrop-blur-xl">
                <a href="{{ route('home') }}" class="font-display text-xl md:text-2xl font-semibold text-white">
                    {{ config('app.name', 'Store') }}
                </a>
                
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('shop') }}" class="text-sm text-muted hover:text-white transition-colors">Shop</a>
                    <a href="{{ route('cart') }}" class="text-sm text-muted hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Cart</span>
                        @if($cartCount > 0)
                            <span class="w-5 h-5 rounded-full bg-accent text-black text-xs flex items-center justify-center font-medium">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    @if(session()->has('admin_id'))
                        <a href="{{ route('admin.dashboard') }}" class="hidden sm:block text-sm text-muted hover:text-white">Dashboard</a>
                    @else
                        <a href="{{ route('admin.login') }}" class="hidden sm:block text-sm text-muted hover:text-white">Login</a>
                    @endif
                    <button id="mobile-menu-btn" class="md:hidden w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-white">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    
    <div id="mobile-menu" class="fixed inset-0 z-40 hidden bg-void/95 backdrop-blur-xl">
        <div class="flex flex-col items-center justify-center h-full gap-8">
            <a href="{{ route('shop') }}" class="font-display text-4xl text-white" onclick="toggleMobileMenu()">Shop</a>
            <a href="{{ route('cart') }}" class="font-display text-4xl text-white flex items-center gap-4" onclick="toggleMobileMenu()">Cart @if($cartCount > 0)<span class="text-accent"> ({{ $cartCount }})</span>@endif</a>
            @if(session()->has('admin_id'))
                <a href="{{ route('admin.dashboard') }}" class="font-display text-4xl text-white" onclick="toggleMobileMenu()">Dashboard</a>
            @else
                <a href="{{ route('admin.login') }}" class="font-display text-4xl text-white" onclick="toggleMobileMenu()">Login</a>
            @endif
        </div>
        <button onclick="toggleMobileMenu()" class="absolute top-6 right-6 w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-surface border-t border-border py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <div>
                    <h3 class="font-display text-2xl font-semibold mb-6">{{ config('app.name', 'Store') }}</h3>
                    <p class="text-muted text-sm leading-relaxed">
                        Premium digital products and curated physical goods. Instant delivery guaranteed.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-sm uppercase tracking-wider text-muted mb-4">Shop</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('shop') }}" class="text-white/70 hover:text-accent transition-colors">All Products</a></li>
                        <li><a href="{{ route('shop') }}?type=digital" class="text-white/70 hover:text-accent transition-colors">Digital</a></li>
                        <li><a href="{{ route('shop') }}?type=physical" class="text-white/70 hover:text-accent transition-colors">Physical</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-sm uppercase tracking-wider text-muted mb-4">Support</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-white/70 hover:text-accent transition-colors">Contact</a></li>
                        <li><a href="#" class="text-white/70 hover:text-accent transition-colors">Privacy</a></li>
                        <li><a href="#" class="text-white/70 hover:text-accent transition-colors">Terms</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-sm uppercase tracking-wider text-muted mb-4">Connect</h4>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center hover:border-accent hover:text-accent transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center hover:border-accent hover:text-accent transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center hover:border-accent hover:text-accent transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-border mt-16 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-muted text-sm">&copy; {{ date('Y') }} {{ config('app.name', 'Store') }}</p>
                <div class="flex items-center gap-2 text-sm text-muted">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    All systems operational
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }
        document.getElementById('mobile-menu-btn')?.addEventListener('click', toggleMobileMenu);
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visible');
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        
        document.querySelectorAll('.reveal, .line-reveal').forEach(el => observer.observe(el));
    </script>
    
    @yield('scripts')
</body>
</html>