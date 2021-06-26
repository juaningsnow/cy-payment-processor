<?php

namespace App\Console\Commands;

use App\Http\Interpreters\XeroInterpreter;
use Illuminate\Console\Command;

class RefreshXeroToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xero:refresh-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Xero Token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dd(app()->make(XeroInterpreter::class)->refreshToken());
        if (app()->make(XeroInterpreter::class)->refreshToken()) {
            echo 'Success';
        } else {
            echo 'Failed';
        }
    }
}
