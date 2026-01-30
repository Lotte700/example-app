<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p>Please login to your account</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus>
                <x-input-error :messages="$errors->get('email')" class="error-msg" />
            </div>

            <div class="form-group mt-4">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" class="form-input" type="password" name="password" required>
                <x-input-error :messages="$errors->get('password')" class="error-msg" />
            </div>

            <div class="form-options mt-4">
                <label class="remember-me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="form-actions mt-6">
                <button type="submit" class="btn-login">
                    {{ __('Log in') }}
                </button>
            </div>
            
            <div class="form-footer mt-6">
                <span>Don't have an account?</span>
                <a href="{{ route('register') }}">Register</a>
            </div>
        </form>
    </div>
</x-guest-layout>