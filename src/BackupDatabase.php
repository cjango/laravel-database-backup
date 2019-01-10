<?php

namespace AsLong\Backup;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->process = new Process(sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path('backups/backup_' . date('Y-m-d') . '.sql')
        ));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            // 删除N天之前的备份
            $directory = storage_path('backups/');
            $files     = scandir($directory);

            foreach ($files as $file) {
                if (!in_array($file, ['.', '..']) && Carbon::now()->diffIndays(substr($file, 7, 10)) >= env('DB_BACKUP_DAYS', 5)) {
                    unlink($directory . $file);
                }
            }

            $this->process->mustRun();

            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has been failed.');
        }
    }
}
