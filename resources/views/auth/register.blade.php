<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h2>Create Account</h2>
            <p>ลงทะเบียนเข้าใช้งานระบบ</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                <x-input-error :messages="$errors->get('name')" class="error-msg" />
            </div>

            <div class="form-group mt-4">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="error-msg" />
            </div>

            <div class="form-group mt-4">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="error-msg" />
            </div>

            <div class="form-group mt-4">
                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password">
                <x-input-error :messages="$errors->get('password_confirmation')" class="error-msg" />
            </div>

            <div class="form-group mt-4">
                <label for="employee_id" class="form-label">Employee</label>
                <select name="employee_id" id="employee_id" class="form-input cursor-pointer">
                    <option value="">-- เลือกพนักงาน --</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->code }} - {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('employee_id')" class="error-msg" />
            </div>

            <div class="form-actions mt-8">
                <button type="submit" class="btn-login">
                    {{ __('Register') }}
                </button>
            </div>

            <div class="form-footer mt-6">
                <a class="already-registered" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>