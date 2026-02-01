<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
            <span class="flex items-center justify-center rounded-md">
                <x-app-logo-icon class="h-20 w-auto text-black dark:text-white" />
            </span>

            <span class="sr-only">{{ config('app.name', 'CAT Tunas') }}</span>
        </a>

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input name="email" :label="__('Email')" :value="old('email')" type="email" required autofocus
                autocomplete="email" placeholder="Masukan email anda" />

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    {{ __('Password') }}
                </label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Masukkan password Anda"
                        class="block w-full px-3 py-2 pr-10 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-900 dark:focus:ring-zinc-100 focus:border-zinc-900 dark:focus:border-zinc-100 transition-all duration-200"
                        required autocomplete="current-password">
                    <button type="button" onclick="togglePasswordVisibility()"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                        aria-label="Toggle password visibility">
                        <i data-lucide="eye" id="password-toggle-icon" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            @if (Route::has('password.request'))
            <div class="flex justify-end -mt-4">
                <a href="{{ route('password.request') }}"
                    class="text-sm text-zinc-900 dark:text-zinc-100 hover:underline" wire:navigate>
                    {{ __('Lupa password?') }}
                </a>
            </div>
            @endif

            <script>
                function togglePasswordVisibility() {
                    const passwordInput = document.getElementById('password');
                    const toggleIcon = document.getElementById('password-toggle-icon');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        toggleIcon.setAttribute('data-lucide', 'eye-off');
                    } else {
                        passwordInput.type = 'password';
                        toggleIcon.setAttribute('data-lucide', 'eye');
                    }

                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            </script>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('ingat saya')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Masuk') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Belum punya akun? hubungi admin') }}</span>
        </div>
        @endif
    </div>
</x-layouts::auth>