@extends('layouts.front')

@section('title', 'Premium Store - Digital & Physical Products')

@section('styles')
<style>
    .hero-title {
        font-size: clamp(4rem, 12vw, 9rem);
        line-height: 0.9;
        letter-spacing: -0.03em;
        font-weight: 600;
    }
    
    .hero-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .hero-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        animation: heroZoom 4s ease-in-out infinite;
    }
    @keyframes heroZoom {
        0% { transform: scale(1) translateX(0); }
        25% { transform: scale(1.03) translateX(-1%); }
        50% { transform: scale(1) translateX(0); }
        75% { transform: scale(1.03) translateX(1%); }
        100% { transform: scale(1) translateX(0); }
    }
    
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(5,5,7,0.85) 0%, rgba(5,5,7,0.5) 100%);
    }
    
    .scroll-indicator {
        animation: scrollBounce 2s ease-in-out infinite;
    }
    @keyframes scrollBounce {
        0%, 100% { transform: translateY(0); opacity: 0.5; }
        50% { transform: translateY(8px); opacity: 1; }
    }
    
    .product-card-img {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 1rem;
        overflow: hidden;
        background: #111;
    }
    .product-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .product-card:hover .product-card-img img {
        transform: scale(1.05);
    }
    
    .product-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(5,5,7,0.8) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .product-card:hover .product-card-overlay { opacity: 1; }
    
    .product-btn {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
        background: #E8FF57;
        color: #000;
        padding: 0.75rem;
        border-radius: 9999px;
        font-weight: 600;
        text-align: center;
        font-size: 0.875rem;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }
    .product-card:hover .product-btn {
        opacity: 1;
        transform: translateY(0);
    }
    
    .digital-badge {
        position: absolute;
        top: 0.75rem;
        left: 0.75rem;
        background: #E8FF57;
        color: #000;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .category-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        aspect-ratio: 16/9;
    }
    .category-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .category-card:hover img { transform: scale(1.05); }
    .category-content {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(5,5,7,0.95) 40%, transparent);
        display: flex;
        align-items: center;
    }
    
    .feature-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 1.5rem;
        padding: 2rem;
        transition: all 0.3s ease;
    }
    .feature-card:hover {
        background: rgba(255,255,255,0.04);
        border-color: rgba(232,255,87,0.2);
    }
</style>
@endsection

@section('content')
<!-- Hero -->
<section class="relative min-h-screen overflow-hidden">
    <!-- Background -->
    <div class="hero-bg">
        <img src="{{ \App\Models\Setting::get('hero_background_image', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1920&h=1080&fit=crop&q=85') }}" alt="Hero">
        <div class="hero-overlay"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 flex items-center justify-center min-h-screen px-4">
        <div class="text-center max-w-4xl mx-auto">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#E8FF57]/30 bg-[#E8FF57]/10 text-sm text-[#E8FF57] mb-8 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-[#E8FF57] animate-pulse"></span>
                All cards & USSD accepted
            </span>
            
            <h1 class="hero-title font-display text-white mb-8">
                <span class="block">Build</span>
                <span class="block text-[#E8FF57]">Better.</span>
            </h1>
            
            <p class="text-lg text-white/60 max-w-lg mx-auto mb-10">
                Premium digital products and curated physical goods. Instant delivery. Secure payments.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#products" class="bg-[#E8FF57] text-black px-10 py-4 rounded-full font-semibold hover:bg-[#C4E044] transition-colors">
                    Shop Now
                </a>
                <a href="{{ route('shop') }}" class="border border-white/30 text-white px-10 py-4 rounded-full font-medium hover:bg-white/10 transition-colors backdrop-blur-sm">
                    Browse All
                </a>
            </div>
            
            <div class="flex items-center justify-center gap-8 mt-12">
                <div>
                    <div class="text-3xl font-bold text-white">{{ \App\Models\Product::where('is_active', 1)->count() }}+</div>
                    <div class="text-sm text-white/50">Products</div>
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div>
                    <div class="text-3xl font-bold text-white">2K+</div>
                    <div class="text-sm text-white/50">Customers</div>
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div>
                    <div class="text-3xl font-bold text-white">100%</div>
                    <div class="text-sm text-white/50">Secure</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2">
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down text-white/50"></i>
        </div>
    </div>
</section>

<!-- Marquee -->
<section class="py-8 border-y border-white/5 overflow-hidden bg-[#0A0A0F]">
    <div class="flex whitespace-nowrap animate-marquee">
        @php $tags = ['Digital Products', 'Instant Access', 'Premium Quality', 'Secure Checkout', 'Nigerian Made', 'World Class', '24/7 Support', 'Fast Delivery']; @endphp
        @foreach(array_merge($tags, $tags, $tags, $tags) as $tag)
            <span class="mx-6 text-xs uppercase tracking-[0.2em] text-white/30">{{ $tag }}</span>
        @endforeach
    </div>
</section>

<!-- Products -->
<section id="products" class="py-8 md:py-14 px-4 md:px-6 bg-[#050507]">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
            <div>
                <span class="text-xs uppercase tracking-[0.2em] text-[#E8FF57] mb-2 block">Featured</span>
                <h2 class="font-display text-3xl md:text-5xl font-semibold text-white">Hot Products</h2>
            </div>
            <a href="{{ route('shop') }}" class="border border-white/20 text-white px-6 py-3 rounded-full text-sm font-medium hover:bg-white/5 transition-colors inline-flex items-center gap-2">
                View All <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse($featuredProducts ?? [] as $index => $product)
            <div class="product-card">
                <div class="product-card-img">
                    @if($product->image)
                        <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                            <i class="fas fa-image text-3xl text-gray-600"></i>
                        </div>
                    @endif
                    <div class="product-card-overlay"></div>
                    @if($product->product_type === 'digital')
                        <span class="digital-badge">Digital</span>
                    @endif
                    <a href="{{ route('product.show', $product->slug) }}" class="product-btn">View Product</a>
                </div>
                <div class="pt-3">
                    <p class="text-[10px] uppercase tracking-wider text-white/40 mb-1">{{ $product->category ?? 'Product' }}</p>
                    <h3 class="text-sm font-medium text-white mb-2 line-clamp-2">{{ $product->name }}</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-[#E8FF57]">₦{{ number_format($product->price, 0) }}</span>
                        <span class="text-[10px] text-white/30">{{ $product->views ?? 0 }} views</span>
                    </div>
                </div>
            </div>
            @empty
            @php
                $placeholder = [
                    ['name' => 'Premium E-Book Bundle', 'price' => 25000, 'cat' => 'E-Books', 'img' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=400&fit=crop'],
                    ['name' => 'Masterclass Course', 'price' => 45000, 'cat' => 'Courses', 'img' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=400&fit=crop'],
                    ['name' => 'Design Assets Pack', 'price' => 15000, 'cat' => 'Design', 'img' => 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?w=400&h=400&fit=crop'],
                    ['name' => 'SaaS Template Kit', 'price' => 35000, 'cat' => 'Templates', 'img' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=400&fit=crop'],
                ];
            @endphp
            @foreach($placeholder as $p)
            <div class="product-card">
                <div class="product-card-img">
                    <img src="{{ $p['img'] }}" alt="{{ $p['name'] }}">
                    <div class="product-card-overlay"></div>
                    <span class="digital-badge">Digital</span>
                    <a href="{{ route('shop') }}" class="product-btn">View Product</a>
                </div>
                <div class="pt-3">
                    <p class="text-[10px] uppercase tracking-wider text-white/40 mb-1">{{ $p['cat'] }}</p>
                    <h3 class="text-sm font-medium text-white mb-2 line-clamp-2">{{ $p['name'] }}</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-[#E8FF57]">₦{{ number_format($p['price'], 0) }}</span>
                        <span class="text-[10px] text-white/30">{{ rand(50, 500) }} views</span>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-8 md:py-14 px-4 md:px-6 bg-[#0A0A0F] border-y border-white/5">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
            <span class="text-xs uppercase tracking-[0.2em] text-[#E8FF57] mb-3 block">Why Us</span>
            <h2 class="font-display text-3xl md:text-5xl font-semibold text-white">Built Different</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-[#E8FF57]/10 flex items-center justify-center mb-5">
                    <i class="fas fa-bolt text-[#E8FF57] text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Instant Access</h3>
                <p class="text-sm text-white/50 leading-relaxed">Get your digital products immediately after payment. No waiting, no delays.</p>
            </div>
            
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center mb-5">
                    <i class="fas fa-shield text-purple-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Secure Payments</h3>
                <p class="text-sm text-white/50 leading-relaxed">All transactions protected by Paystack's enterprise-grade security.</p>
            </div>
            
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center mb-5">
                    <i class="fas fa-headset text-blue-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">24/7 Support</h3>
                <p class="text-sm text-white/50 leading-relaxed">Our team is always available via WhatsApp, email, or support portal.</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-8 md:py-14 px-4 md:px-6 bg-[#050507]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <span class="text-xs uppercase tracking-[0.2em] text-[#E8FF57] mb-3 block">Categories</span>
            <h2 class="font-display text-3xl md:text-5xl font-semibold text-white">Shop by Type</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('shop') }}?type=digital" class="category-card">
                <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=400&fit=crop&q=80" alt="Digital Products">
                <div class="category-content">
                    <div class="p-6 md:p-8">
                        <span class="text-[10px] uppercase tracking-[0.2em] text-[#E8FF57] mb-2 block">Digital</span>
                        <h3 class="font-display text-xl md:text-2xl font-semibold text-white mb-1">E-Books & Courses</h3>
                        <p class="text-sm text-white/50">Instant download</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('shop') }}?type=physical" class="category-card">
                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=400&fit=crop&q=80" alt="Physical Products">
                <div class="category-content">
                    <div class="p-6 md:p-8">
                        <span class="text-[10px] uppercase tracking-[0.2em] text-[#E8FF57] mb-2 block">Physical</span>
                        <h3 class="font-display text-xl md:text-2xl font-semibold text-white mb-1">Curated Goods</h3>
                        <p class="text-sm text-white/50">Nationwide delivery</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-8 md:py-14 px-4 md:px-6 bg-[#0A0A0F]">
    <div class="max-w-3xl mx-auto text-center">
        <span class="text-xs uppercase tracking-[0.2em] text-[#E8FF57] mb-4 block">Get Started</span>
        <h2 class="font-display text-3xl md:text-6xl font-semibold text-white mb-4">Ready to Build?</h2>
        <p class="text-lg text-white/50 mb-10">Join thousands of customers who trust us for premium products.</p>
        <a href="{{ route('shop') }}" class="bg-[#E8FF57] text-black px-10 py-4 rounded-full font-semibold inline-flex items-center gap-2 hover:bg-[#C4E044] transition-colors">
            Start Shopping <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>
@endsection

@section('scripts')
<style>
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-marquee {
    animation: marquee 35s linear infinite;
}
</style>
@endsection