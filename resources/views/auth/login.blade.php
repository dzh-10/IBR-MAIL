@extends('layouts.app_corpmail')

@section('title', 'Login - CorpMail')

@section('content')
<style>
    .login-wrapper {
        min-height: 100vh;
        width: 100vw;
        display: flex;
        justify-content: center;
        align-items: center;
        background: radial-gradient(circle at 10% 20%, rgba(98, 114, 245, 0.1) 0%, rgba(121, 243, 242, 0.05) 90.1%),
                    linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        position: relative;
        overflow: hidden;
    }

    .login-wrapper::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
        top: -100px;
        left: -100px;
        opacity: 0.3;
        filter: blur(50px);
    }

    .login-card {
        width: 440px;
        padding: 48px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        color: #ffffff;
    }

    .login-logo {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
        background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -1px;
    }

    .login-subtitle {
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 36px;
    }

    .form-group {
        text-align: left;
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 8px;
    }

    .form-control-wrap {
        position: relative;
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(15, 23, 42, 0.6);
        color: #ffffff;
        font-family: var(--font-primary);
        font-size: 14px;
        outline: none;
        transition: var(--transition-smooth);
    }

    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .login-btn {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: var(--radius-sm);
        background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%);
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        font-family: var(--font-primary);
        transition: var(--transition-smooth);
        margin-top: 12px;
    }

    .login-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
    }

    .error-alert {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: var(--radius-sm);
        padding: 12px 16px;
        color: #fca5a5;
        font-size: 13px;
        text-align: left;
        margin-bottom: 24px;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        @if(setting('app_logo'))
            <img src="{{ setting('app_logo') }}" alt="Logo" style="max-height: 48px; margin-bottom: 16px; border-radius: 8px;">
        @endif
        <div class="login-logo">{{ setting('app_name', 'Messagerie') }}</div>
        <div class="login-subtitle">Connect to your internal and external communication center</div>

        @if ($errors->any())
            <div class="error-alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="form-control-wrap">
                    <input type="email" name="email" class="form-control" placeholder="admin@company.local" required autofocus value="{{ old('email') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="form-control-wrap">
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="login-btn">Sign In</button>
        </form>
    </div>
</div>


@endsection
