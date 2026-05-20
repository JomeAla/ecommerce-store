@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Store Settings')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
            <div class="border-b border-slate-700 p-4 bg-slate-700/50">
                <h3 class="text-lg font-semibold text-white">General Settings</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Store Name</label>
                    <input type="text" name="settings[store_name]" value="{{ $settings['general']->where('key', 'store_name')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Store Email</label>
                    <input type="email" name="settings[store_email]" value="{{ $settings['general']->where('key', 'store_email')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Store Phone</label>
                    <input type="text" name="settings[store_phone]" value="{{ $settings['general']->where('key', 'store_phone')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Currency Code</label>
                    <input type="text" name="settings[currency]" value="{{ $settings['general']->where('key', 'currency')->first()?->value ?: 'NGN' }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Currency Symbol</label>
                    <input type="text" name="settings[currency_symbol]" value="{{ $settings['general']->where('key', 'currency_symbol')->first()?->value ?: '₦' }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Store Address</label>
                    <textarea name="settings[store_address]" rows="3" 
                              class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">{{ $settings['general']->where('key', 'store_address')->first()?->value }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden mt-6">
            <div class="border-b border-slate-700 p-4 bg-slate-700/50">
                <h3 class="text-lg font-semibold text-white">Payment Settings</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Paystack Public Key</label>
                    <input type="text" name="settings[paystack_public_key]" value="{{ $settings['payment']->where('key', 'paystack_public_key')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Paystack Secret Key</label>
                    <input type="password" name="settings[paystack_secret_key]" value="{{ $settings['payment']->where('key', 'paystack_secret_key')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden mt-6">
            <div class="border-b border-slate-700 p-4 bg-slate-700/50">
                <h3 class="text-lg font-semibold text-white">Email Settings (SMTP)</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Mail Driver</label>
                    <select name="settings[mail_mailer]" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="smtp" {{ ($settings['email']->where('key', 'mail_mailer')->first()?->value ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ $settings['email']->where('key', 'mail_mailer')->first()?->value === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="log" {{ $settings['email']->where('key', 'mail_mailer')->first()?->value === 'log' ? 'selected' : '' }}>Log (Debug)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">SMTP Host</label>
                    <input type="text" name="settings[mail_host]" value="{{ $settings['email']->where('key', 'mail_host')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">SMTP Port</label>
                    <input type="text" name="settings[mail_port]" value="{{ $settings['email']->where('key', 'mail_port')->first()?->value ?: '587' }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">SMTP Username</label>
                    <input type="text" name="settings[mail_username]" value="{{ $settings['email']->where('key', 'mail_username')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">SMTP Password</label>
                    <input type="password" name="settings[mail_password]" value="{{ $settings['email']->where('key', 'mail_password')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Encryption</label>
                    <select name="settings[mail_encryption]" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 focus:outline-none focus:border-indigo-500">
                        <option value="tls" {{ ($settings['email']->where('key', 'mail_encryption')->first()?->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $settings['email']->where('key', 'mail_encryption')->first()?->value === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="">None</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">From Email</label>
                    <input type="email" name="settings[mail_from_address]" value="{{ $settings['email']->where('key', 'mail_from_address')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">From Name</label>
                    <input type="text" name="settings[mail_from_name]" value="{{ $settings['email']->where('key', 'mail_from_name')->first()?->value }}" 
                           class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-200 placeholder-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                <i class="bi bi-save mr-2"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection