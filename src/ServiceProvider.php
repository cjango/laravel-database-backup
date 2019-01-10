<?php

namespace AsLong\Backup;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{

    protected $commands = [
        BackupDatabase::class,
    ];

    public function boot()
    {
        $this->commands($this->commands);
    }

    public function register()
    {

    }
}
