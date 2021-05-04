<?php

declare(strict_types=1);

namespace BaseCode\Common\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'c1:install-audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install BaseCode Common Audit Module.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment('Installing Auth');
        $this->callSilent('vendor:publish', [
            '--provider' => 'BaseCode\Common\Providers\C1SCommonServiceProvider',
            '--tag' => 'config',
            '--force'
        ]);
        $this->info('✔️  Created config/audit.php');
    }
}
