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
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Backup the database';

    /**
     * 备份目录
     * @var string
     */
    protected $backDir;

    /**
     * 备份留存时间（天）
     * @var integer
     */
    protected $days;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->backDir = storage_path('backups/');
        $this->days    = 7;

        if (!is_dir($this->backDir)) {
            mkdir($this->backDir, 0755, true);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->process = new Process(sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $this->backDir . 'backup_' . date('Y-m-d_H:i:s') . '.sql'
        ));

        try {
            // 删除N天之前的备份
            $backDir = $this->backDir;
            $files   = scandir($backDir);

            foreach ($files as $file) {
                if (!in_array($file, ['.', '..']) && Carbon::now()->diffIndays(substr($file, 7, 10)) >= $this->days) {
                    unlink($backDir . $file);
                }
            }

            $this->process->mustRun();

            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has been failed.');
        }
    }
}
