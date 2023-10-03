<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;


class RunMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:custom';
    protected $description = 'Run custom migrations';
    

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running migrations...');
        $exitCode = Artisan::call('migrate', [
            '--database' => 'your_database_connection', // Change to your desired database connection
            '--force' => true, // Add other migration options as needed
        ]);
        
        if ($exitCode === 0) {
            $this->info('Migrations completed successfully.');
        } else {
            $this->error('Migrations failed.');
        }
    }
    
}
