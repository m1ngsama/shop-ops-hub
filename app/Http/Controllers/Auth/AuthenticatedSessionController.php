<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, AuditLogService $auditLogService): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => '请输入登录邮箱。',
            'email.email' => '邮箱格式不正确。',
            'password.required' => '请输入登录密码。',
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => '尝试次数过多，请在 '.RateLimiter::availableIn($throttleKey).' 秒后重试。',
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => '账号或密码不正确。',
            ]);
        }

        $request->session()->regenerate();
        RateLimiter::clear($throttleKey);

        if (! $request->user()?->canAccessAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => '当前账号没有后台访问权限。',
            ]);
        }

        $auditLogService->record('auth.login', $request, meta: [
            'role' => $request->user()?->role,
            'remember' => $request->boolean('remember'),
        ]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request, AuditLogService $auditLogService): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            $auditLogService->record('auth.logout', $request, $user, meta: [
                'role' => $user->role,
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.home');
    }
}
