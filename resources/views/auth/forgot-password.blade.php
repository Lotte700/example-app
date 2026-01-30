<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h2>Reset Password</h2>
            <p>{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus>
                <x-input-error :messages="$errors->get('email')" class="error-msg" />
            </div>

            <div class="form-actions mt-6">
                <button type="submit" class="btn-login">
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>

            <div class="form-footer mt-6">
                <a class="already-registered" href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>