<x-seller-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('seller.login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[#1F3B73] font-medium" />
            <x-text-input
                id="email"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-[#FFA500] focus:ring-[#FFA500]"
                type="email"
                name="email"
                :value="old('email')"
                required autofocus autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-[#1F3B73] font-medium" />
            <x-text-input
                id="password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-[#FFA500] focus:ring-[#FFA500]"
                type="password"
                name="password"
                required autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center text-sm text-gray-600">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-[#1F3B73] shadow-sm focus:ring-[#FFA500]"
                    name="remember"
                >
                <span class="ms-2"> {{ __('Remember me') }} </span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-[#1F3B73] hover:underline">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <x-primary-button class="bg-[#1F3B73] hover:bg-[#FFA500] transition duration-300">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-seller-guest-layout>
