<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm p-4 w-100" style="max-width: 420px;">
        <!-- Logo -->
        <div class="d-flex justify-content-center mb-3">
            <img src="{{ asset('images/gmall.png') }}" alt="Logo" style="height: 30px;">
        </div>

        <h3 class="text-center mb-4"><strong>Sign in</strong></h3>

        <!-- Session Status -->
        <x-auth-session-status class="mb-3" :status="session('status')" />

        <form wire:submit="login">
            <!-- Email -->
            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input wire:model="form.email" id="email" class="form-control" type="email" required autofocus
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </div>

            <div class="mb-3 position-relative">
                <x-input-label for="password" :value="__('Password')" />

                <div class="position-relative">
                    <x-text-input wire:model="form.password" id="password" class="form-control" type="password" required
                        autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />

                    <!-- Eye Icon -->
                    <span class="position-absolute top-50 end-0 translate-middle-y pe-3" style="cursor: pointer;"
                        onclick="togglePasswordVisibility()">
                        <!-- Open Eye (Outline) -->
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            stroke="currentColor" stroke-width="1.5" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z" />
                            <path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        </svg>

                        <!-- Closed Eye (Outline) -->
                        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            stroke="currentColor" stroke-width="1.5" class="bi bi-eye-slash d-none" viewBox="0 0 16 16">
                            <path
                                d="M13.359 11.238l1.417 1.417M2.5 2.5l11 11M1.293 1.293l13.414 13.414M8 3C12.04 3 15.06 8.02 15.06 8.02c-.374.748-.83 1.451-1.343 2.03M2.5 2.5C.834 4.08 0 8 0 8s3 5.5 8 5.5a7.968 7.968 0 0 0 5.931-2.586" />
                            <path d="M5.244 6.244a3 3 0 0 0 4.243 4.243" />
                        </svg>
                    </span>
                </div>

                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
                <input wire:model="form.remember" class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label" for="remember">
                    {{ __('Remember me') }}
                </label>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn w-100 text-white" style="background-color: #1F364A;">
                <strong>{{ __('Log in') }}</strong>
            </button>
        </form>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
        <div class="text-end mt-2">
            <a href="{{ route('password.request') }}" class="text-decoration-none text-secondary small">
                {{ __('Forgot your password?') }}
            </a>
        </div>
        @endif

        <!-- Sign Up -->
        <div class="text-center mt-4">
            <p class="mb-0 small">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">
                    {{ __('Sign Up') }}
                </a>
            </p>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeOpen.classList.toggle('d-none', !isPassword);
        eyeClosed.classList.toggle('d-none', isPassword);
    }
</script>