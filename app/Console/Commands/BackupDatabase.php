<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the database';

    public function handle()
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        // Ensure backup directory exists
        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // Create backup command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($database),
            escapeshellarg($path)
        );
        
        system($command);
        
        $this->info("Database backed up to: {$path}");
        
        // Keep only last 30 backups
        $backups = glob(storage_path('app/backups/*.sql'));
        if (count($backups) > 30) {
            array_map('unlink', array_slice($backups, 0, count($backups) - 30));
        }
    }
}