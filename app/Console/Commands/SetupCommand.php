<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'food:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        while (true){
            try {
                $this->call('migrate');
                $this->call('db:seed');
                break;
            }catch (\Exception $exception) {
                $this->error($exception->getMessage());
                sleep(1);
            }
        }
        return CommandAlias::SUCCESS;
    }
}
