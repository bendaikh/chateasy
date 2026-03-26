<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white mb-2">Create your account</h2>
        <p class="text-gray-400">Start automating your WhatsApp today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
            <input 
                id="name" 
                type="text" 
                name="name" 
                value="{{ old('name') }}"
                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" 
                placeholder="John Doe"
                required 
                autofocus 
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}"
                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" 
                placeholder="you@example.com"
                required 
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
            <input 
                id="password" 
                type="password" 
                name="password"
                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" 
                placeholder="••••••••"
                required 
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation"
                class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" 
                placeholder="••••••••"
                required 
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Terms -->
        <div class="text-xs text-gray-400">
            By creating an account, you agree to our 
            <a href="#" class="text-emerald-400 hover:text-emerald-300">Terms of Service</a> 
            and 
            <a href="#" class="text-emerald-400 hover:text-emerald-300">Privacy Policy</a>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit"
            class="w-full py-3 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition shadow-lg shadow-emerald-500/30"
        >
            Create Account
        </button>
    </form>

    <!-- Login Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-emerald-400 hover:text-emerald-300 font-semibold transition">
                Sign in
            </a>
        </p>
    </div>

    <x-slot name="footerText">
        <a href="{{ url('/') }}" class="text-emerald-400 hover:text-emerald-300 transition">
            ← Back to home
        </a>
    </x-slot>
</x-guest-layout>
