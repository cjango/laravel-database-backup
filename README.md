# Laravel Database Backup

## 1.安装
> composer require aslong/laravel-database-backup

## 2.使用
> php artisan db:backup

## 3.扩展使用

开启定时任务

~~~php
<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('db:backup')->daily();
    }
}
~~~
