@extends('layouts.front')

@section('title', 'Checkout - ' . config('app.name', 'Online Store'))

@section('content')
<div class="bg-gray-100 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-amber-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('cart') }}" class="hover:text-amber-600">Cart</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">Checkout</span>
        </nav>
    </div>
</div>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Checkout</h1>
        
        @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <form id="checkout-form" action="{{ route('checkout.init') }}" method="POST">
                    @csrf
                    
                    <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Contact Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="name" value="{{ old('name', $customerData['name'] ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email', $customerData['email'] ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="john@example.com">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" name="phone" value="{{ old('phone', $customerData['phone'] ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="+234 800 123 4567">
                            <p class="text-sm text-gray-500 mt-1">Required for payment confirmation</p>
                        </div>
                    </div>

                    @if($hasPhysicalProducts)
                    <div class="bg-white rounded-xl shadow-lg p-8 mb-6" id="shipping-section">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Shipping Address</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                <select name="shipping_address[country]" id="shipping-country" onchange="updateShippingRates()" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <option value="">Select Country</option>
                                    <option value="Nigeria" selected>Nigeria</option>
                                    <option value="Ghana">Ghana</option>
                                    <option value="Kenya">Kenya</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="United States">United States</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Other">Other</option>
                                </select>
                                <input type="hidden" name="shipping_address[country_code]" id="country-code" value="NG">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State / Region *</label>
                                <input type="text" name="shipping_address[state]" id="shipping-state" onchange="updateShippingRates()" placeholder="e.g., Lagos, Abuja" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                <input type="text" name="shipping_address[city]" id="shipping-city" placeholder="e.g., Ikeja" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                <input type="text" name="shipping_address[postal_code]" id="shipping-postal" placeholder="e.g., 100001" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                            <input type="text" name="shipping_address[address_line1]" required placeholder="Street address, P.O. box" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                            <input type="text" name="shipping_address[address_line2]" placeholder="Apartment, suite, unit, building, floor, etc." class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8 mb-6" id="shipping-methods-section">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Shipping Method</h2>
                        
                        <div id="shipping-methods-loader" class="text-center py-4">
                            <p class="text-gray-500">Enter your shipping address above to see available methods</p>
                        </div>
                        
                        <div id="shipping-methods-list" class="space-y-4 hidden">
                            @foreach($shippingMethods as $method)
                            <label class="block border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-amber-500 transition-colors shipping-method-option" data-method-id="{{ $method['id'] ?? 0 }}" data-cost="{{ $method['cost'] ?? 0 }}">
                                <div class="flex items-center">
                                    <input type="radio" name="shipping_method_id" value="{{ $method['id'] ?? 0 }}" class="w-4 h-4 text-amber-600 focus:ring-amber-500" {{ $loop->first ? 'checked' : '' }}>
                                    <div class="ml-3 flex-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium text-gray-900">{{ $method['name'] ?? 'Standard Shipping' }}</span>
                                            <span class="font-semibold {{ ($method['is_free'] ?? false) ? 'text-green-600' : 'text-gray-900' }}">
                                                {{ $method['is_free'] ?? false ? 'Free' : '₦' . number_format($method['cost'] ?? 0, 2) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">{{ $method['delivery_estimate'] ?? 'Estimated delivery: 3-5 business days' }}</p>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <div class="border-t pt-6">
                        <button type="submit" id="paystack-button" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-4 px-6 rounded-lg transition flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="bi bi-credit-card me-2"></i>
                            <span>Pay with Paystack - ₦{{ number_format($cartTotal, 2) }}</span>
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-500 text-center mt-4">
                        <i class="bi bi-shield-lock me-1"></i> Secure payment powered by Paystack
                    </p>
                </form>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h2>
                    
                    <div class="space-y-4 mb-6 max-h-64 overflow-y-auto">
                        @foreach($cartItems as $item)
                        <div class="flex items-center justify-between pb-4 border-b">
                            <div class="flex items-center">
                                @if($item->product && $item->product->image_url)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded mr-3">
                                @else
                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center mr-3">
                                    <i class="bi bi-image text-gray-400"></i>
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $item->product->name ?? 'Product' }}</p>
                                    <p class="text-gray-500 text-sm">Qty: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <span class="font-medium">₦{{ number_format($item->subtotal, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="space-y-3 border-t pt-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>₦{{ number_format($cartSubtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600" id="shipping-row">
                            <span>Shipping</span>
                            <span id="shipping-cost-display" class="{{ $hasPhysicalProducts ? 'text-gray-400' : 'text-green-600' }}">{{ $hasPhysicalProducts ? 'Calculated at next step' : 'Free' }}</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between text-xl font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-amber-600" id="total-display">₦{{ number_format($cartTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @else
        <div class="text-center py-16 bg-white rounded-xl shadow-lg">
            <i class="bi bi-cart-x text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h3>
            <p class="text-gray-500 mb-6">Add some products to your cart before checking out.</p>
            <a href="{{ route('shop') }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-3 rounded-lg transition">
                <i class="bi bi-bag me-2"></i> Continue Shopping
            </a>
        </div>
        @endif
    </div>
</section>

@section('scripts')
@if($cartItems->count() > 0)
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
const subtotal = {{ $cartSubtotal }};
let currentShippingCost = {{ $shippingCost }};

function updateShippingRates() {
    const country = document.getElementById('shipping-country').value;
    const countryCode = getCountryCode(country);
    const state = document.getElementById('shipping-state').value;
    const city = document.getElementById('shipping-city').value;
    
    document.getElementById('country-code').value = countryCode;
    
    if (!country) return;
    
    const loader = document.getElementById('shipping-methods-loader');
    const methodsList = document.getElementById('shipping-methods-list');
    
    loader.innerHTML = '<p class="text-gray-500"><i class="bi bi-hourglass-split me-2"></i>Loading shipping options...</p>';
    
    fetch('{{ route("checkout.shipping-rates") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            country: country,
            country_code: countryCode,
            state: state,
            city: city
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.methods.length > 0) {
            let html = '';
            data.methods.forEach((method, index) => {
                const isFree = method.is_free;
                const costText = isFree ? 'Free' : '₦' + number_format(method.cost, 2);
                html += `
                    <label class="block border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-amber-500 transition-colors shipping-method-option" data-method-id="${method.id}" data-cost="${method.cost}">
                        <div class="flex items-center">
                            <input type="radio" name="shipping_method_id" value="${method.id}" class="w-4 h-4 text-amber-600 focus:ring-amber-500" ${index === 0 ? 'checked' : ''}>
                            <div class="ml-3 flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900">${method.name}</span>
                                    <span class="font-semibold ${isFree ? 'text-green-600' : 'text-gray-900'}">${costText}</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">${method.delivery_estimate}</p>
                            </div>
                        </div>
                    </label>
                `;
            });
            
            methodsList.innerHTML = html;
            methodsList.classList.remove('hidden');
            loader.classList.add('hidden');
            
            currentShippingCost = data.methods[0].cost;
            updateTotals();
            
            methodsList.querySelectorAll('.shipping-method-option').forEach(option => {
                option.addEventListener('click', function() {
                    methodsList.querySelectorAll('.shipping-method-option').forEach(o => {
                        o.classList.remove('border-amber-500', 'bg-amber-50');
                    });
                    this.classList.add('border-amber-500', 'bg-amber-50');
                    currentShippingCost = parseFloat(this.dataset.cost);
                    updateTotals();
                });
            });
            
            if (data.methods[0]) {
                methodsList.querySelector('.shipping-method-option').classList.add('border-amber-500', 'bg-amber-50');
            }
        } else if (!data.has_physical) {
            loader.innerHTML = '<p class="text-green-600"><i class="bi bi-check-circle me-2"></i>No shipping required for digital products</p>';
            currentShippingCost = 0;
            updateTotals();
        } else {
            loader.innerHTML = '<p class="text-gray-500">No shipping methods available for this location. Please contact us for assistance.</p>';
        }
    })
    .catch(error => {
        loader.innerHTML = '<p class="text-red-500">Error loading shipping options. Please try again.</p>';
    });
}

function getCountryCode(country) {
    const codes = {
        'Nigeria': 'NG',
        'Ghana': 'GH',
        'Kenya': 'KE',
        'South Africa': 'ZA',
        'United States': 'US',
        'United Kingdom': 'GB',
        'Canada': 'CA'
    };
    return codes[country] || 'XX';
}

function number_format(num, decimals) {
    return parseFloat(num).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function updateTotals() {
    const total = subtotal + currentShippingCost;
    const shippingCostDisplay = document.getElementById('shipping-cost-display');
    const totalDisplay = document.getElementById('total-display');
    const button = document.getElementById('paystack-button');
    
    if (currentShippingCost === 0) {
        shippingCostDisplay.textContent = 'Free';
        shippingCostDisplay.classList.remove('text-gray-400');
        shippingCostDisplay.classList.add('text-green-600');
    } else {
        shippingCostDisplay.textContent = '₦' + number_format(currentShippingCost, 2);
        shippingCostDisplay.classList.add('text-gray-400');
        shippingCostDisplay.classList.remove('text-green-600');
    }
    
    totalDisplay.textContent = '₦' + number_format(total, 2);
    button.querySelector('span').textContent = 'Pay with Paystack - ₦' + number_format(total, 2);
}

document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const name = form.name.value;
    const email = form.email.value;
    const phone = form.phone.value;
    const button = document.getElementById('paystack-button');
    
    if (!name || !email || !phone) {
        alert('Please fill in all required fields');
        return;
    }
    
    const hasPhysical = {{ $hasPhysicalProducts ? 'true' : 'false' }};
    if (hasPhysical) {
        const country = form.querySelector('[name="shipping_address[country]"]').value;
        const state = form.querySelector('[name="shipping_address[state]"]').value;
        const city = form.querySelector('[name="shipping_address[city]"]').value;
        const addressLine1 = form.querySelector('[name="shipping_address[address_line1]"]').value;
        
        if (!country || !state || !city || !addressLine1) {
            alert('Please fill in all shipping address fields');
            return;
        }
    }
    
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Processing...';
    
    const total = subtotal + currentShippingCost;
    const selectedMethod = form.querySelector('input[name="shipping_method_id"]:checked');
    const shippingMethodId = selectedMethod ? selectedMethod.value : null;
    
    const shippingAddress = hasPhysical ? {
        country: form.querySelector('[name="shipping_address[country]"]').value,
        country_code: document.getElementById('country-code').value,
        state: form.querySelector('[name="shipping_address[state]"]').value,
        city: form.querySelector('[name="shipping_address[city]"]').value,
        postal_code: form.querySelector('[name="shipping_address[postal_code]"]').value,
        address_line1: form.querySelector('[name="shipping_address[address_line1]"]').value,
        address_line2: form.querySelector('[name="shipping_address[address_line2]"]').value,
    } : null;
    
    fetch('{{ route("checkout.init") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            email: email,
            phone: phone,
            shipping_method_id: shippingMethodId,
            shipping_address: shippingAddress
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        
        if (typeof PaystackPop !== 'undefined') {
            const paystack = PaystackPop.setup({
                key: '{{ $paystackKey }}',
                email: email,
                amount: data.amount,
                reference: data.reference,
                currency: 'NGN',
                callback: function(response) {
                    window.location.href = '{{ route("checkout.success") }}?reference=' + response.reference;
                },
                onClose: function() {
                    button.disabled = false;
                    button.innerHTML = '<i class="bi bi-credit-card me-2"></i><span>Pay with Paystack - ₦' + number_format(total, 2) + '</span>';
                }
            });
            paystack.openIframe();
        } else {
            alert('Payment system is loading. Please try again.');
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-credit-card me-2"></i><span>Pay with Paystack - ₦' + number_format(total, 2) + '</span>';
        }
    })
    .catch(error => {
        alert(error.message || 'An error occurred. Please try again.');
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-credit-card me-2"></i><span>Pay with Paystack - ₦' + number_format(total, 2) + '</span>';
    });
});
</script>
@endif
@endsection