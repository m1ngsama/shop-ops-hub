<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Command\Command;

Artisan::command('ops:bootstrap-admin {--email=} {--password=} {--rotate-password}', function () {
    $email = trim((string) ($this->option('email') ?: config('shop_ops.admin_email')));
    $password = (string) ($this->option('password') ?: config('shop_ops.admin_password'));

    if ($email === '' || $password === '') {
        $this->error('请先设置 SHOP_OPS_ADMIN_EMAIL 和 SHOP_OPS_ADMIN_PASSWORD，或在命令中通过选项传入。');

        return Command::FAILURE;
    }

    $user = User::query()->firstOrNew(['email' => $email]);
    $isNewUser = ! $user->exists;

    $user->fill([
        'name' => $user->name ?: '系统管理员',
        'role' => 'admin',
        'is_active' => true,
    ]);

    if ($isNewUser || $this->option('rotate-password')) {
        $user->password = Hash::make($password);
    }

    $user->save();

    $this->info($isNewUser ? '管理员账号已创建。' : '管理员账号已校准。');
    $this->table(['字段', '值'], [
        ['email', $user->email],
        ['role', $user->role],
        ['is_active', $user->is_active ? 'true' : 'false'],
        ['password_rotated', ($isNewUser || $this->option('rotate-password')) ? 'true' : 'false'],
    ]);

    return Command::SUCCESS;
})->purpose('Create or normalize the production admin account without touching business data');

Artisan::command('ops:check', function () {
    $checks = [];
    $push = function (string $name, bool $ok, string $detail) use (&$checks): void {
        $checks[] = [$name, $ok ? 'OK' : 'FAIL', $detail];
    };

    $appUrl = trim((string) config('app.url'));
    $appKey = trim((string) config('app.key'));
    $sessionCookie = trim((string) config('session.cookie'));
    $cachePrefix = trim((string) config('cache.prefix'));
    $redisPrefix = trim((string) config('database.redis.options.prefix'));
    $queueConnection = (string) config('queue.default');
    $apiToken = trim((string) config('shop_ops.api_token'));
    $adminEmail = trim((string) config('shop_ops.admin_email'));

    $push('APP_URL', str_starts_with($appUrl, 'https://'), $appUrl !== '' ? $appUrl : 'missing');
    $push('APP_KEY', $appKey !== '' && ! str_contains($appKey, 'replace-this'), $appKey !== '' ? 'configured' : 'missing');
    $push('APP_DEBUG', ! (bool) config('app.debug'), (bool) config('app.debug') ? 'enabled' : 'disabled');
    $push('Session Cookie', $sessionCookie !== '' && ! str_starts_with($sessionCookie, '-'), $sessionCookie !== '' ? $sessionCookie : 'missing');
    $push('Cache Prefix', $cachePrefix !== '', $cachePrefix !== '' ? $cachePrefix : 'missing');
    $push('Redis Prefix', $redisPrefix !== '', $redisPrefix !== '' ? $redisPrefix : 'missing');
    $push('API Token', $apiToken !== '' && ! str_contains($apiToken, 'change-me'), $apiToken !== '' ? 'configured' : 'missing');
    $push('Queue Connection', in_array($queueConnection, ['database', 'redis'], true), $queueConnection);

    try {
        DB::select('select 1');
        $push('Database', true, (string) config('database.default'));
    } catch (Throwable $exception) {
        $push('Database', false, $exception->getMessage());
    }

    if (config('cache.default') === 'redis' || $queueConnection === 'redis') {
        try {
            app('redis')->connection()->ping();
            $push('Redis', true, (string) config('database.redis.default.host'));
        } catch (Throwable $exception) {
            $push('Redis', false, $exception->getMessage());
        }
    }

    try {
        $push(
            'Admin Account',
            $adminEmail !== '' && User::query()->where('email', $adminEmail)->exists(),
            $adminEmail !== '' ? $adminEmail : 'missing'
        );
    } catch (Throwable $exception) {
        $push('Admin Account', false, $exception->getMessage());
    }

    $this->table(['检查项', '状态', '详情'], $checks);

    $hasFailure = collect($checks)->contains(fn (array $check): bool => $check[1] === 'FAIL');

    return $hasFailure ? Command::FAILURE : Command::SUCCESS;
})->purpose('Run a production readiness check for critical runtime configuration');

Artisan::command('dev:check', function () {
    $checks = [];
    $push = function (string $name, bool $ok, string $detail) use (&$checks): void {
        $checks[] = [$name, $ok ? 'OK' : 'FAIL', $detail];
    };

    $appUrl = trim((string) config('app.url'));
    $appKey = trim((string) config('app.key'));
    $queueConnection = (string) config('queue.default');
    $dbConnection = (string) config('database.default');
    $adminEmail = trim((string) config('shop_ops.admin_email'));

    $push('APP_ENV', config('app.env') === 'local', (string) config('app.env'));
    $push('APP_URL', $appUrl !== '', $appUrl !== '' ? $appUrl : 'missing');
    $push('APP_KEY', $appKey !== '', $appKey !== '' ? 'configured' : 'missing');
    $push('APP_DEBUG', (bool) config('app.debug'), (bool) config('app.debug') ? 'enabled' : 'disabled');
    $push('Queue Connection', in_array($queueConnection, ['database', 'redis'], true), $queueConnection);

    try {
        DB::select('select 1');
        $push('Database', true, $dbConnection);
    } catch (Throwable $exception) {
        $push('Database', false, $exception->getMessage());
    }

    try {
        $push(
            'Admin Account',
            $adminEmail !== '' && User::query()->where('email', $adminEmail)->exists(),
            $adminEmail !== '' ? $adminEmail : 'missing'
        );
    } catch (Throwable $exception) {
        $push('Admin Account', false, $exception->getMessage());
    }

    $this->table(['检查项', '状态', '详情'], $checks);

    $hasFailure = collect($checks)->contains(fn (array $check): bool => $check[1] === 'FAIL');

    return $hasFailure ? Command::FAILURE : Command::SUCCESS;
})->purpose('Run a local development readiness check');
