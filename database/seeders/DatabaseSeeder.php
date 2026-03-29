<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            $this->call(CommerceOpsSeeder::class);

            return;
        }

        $this->command?->warn('生产环境默认不会写入演示数据。');
        $this->command?->line('如需初始化管理员，请执行: php artisan ops:bootstrap-admin');
    }
}
