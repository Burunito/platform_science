<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TestController;

class CodingTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is for a coding test to get the inputs througth console';

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
        $fileDestinations = $this->ask('Type path of the destinations file', './public/destinations.txt');
        $fileDrivers = $this->ask('Type path of the drivers file', './public/drivers.txt');
        $testController = new TestController($fileDestinations, $fileDrivers);
        $result = $testController->test();
        if(is_array($result)){
            $this->newLine();
            $this->info('Total suitability score is: '.$result['total']);
            $this->newLine();
            foreach($result['result'] as $combination){
                $this->newLine();
                $this->info('Direction: '.$combination->destination);
                $this->info('Driver: '.$combination->driver);
                $this->info('Score: '.$combination->score);
            }
        } else {
            $this->error($result);
        }
    }
}
