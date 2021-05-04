<?php

declare(strict_types=1);

namespace BaseCode\Auth\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'c1:install-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install BaseCode Auth Module.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment('Installing Auth');
        $this->callSilent('vendor:publish', [
            '--provider' => 'BaseCode\Auth\Providers\C1SAuthServiceProvider',
            '--tag' => 'config',
            '--force'
        ]);
        $this->info('✔️  Created config/permission.php');

        $this->callSilent('vendor:publish', [
            '--provider' => 'BaseCode\Auth\Providers\C1SAuthServiceProvider',
            '--tag' => 'migrations',
            '--force'
        ]);
        $this->info('✔️  Created migrations. Remember to run [php artisan migrate]!');
    }
}
