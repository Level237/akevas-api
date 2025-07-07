<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLaravelLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear';
    protected $description = 'Vide le fichier laravel.log';

    /**
     * The console command description.
     *
     * @var string
     */
    

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath)) {
            File::put($logPath, ''); // Vide le fichier
            $this->info('Le fichier laravel.log a été vidé avec succès.');
        } else {
            $this->warn('Le fichier laravel.log n\'existe pas.');
        }

        return 0;
    }
}
