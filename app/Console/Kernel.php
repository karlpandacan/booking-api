<?php
/**
* @author : Rovi Roy Cruz
* 2020-02-13
* create Backup for Knowledgebase Counter
* delete all data for the month for Knowledgebase Counter
* 2020-02-18
* create backup for Advisory Counter
* delete all data for the month for Advisory Counter
*/
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Models\KbCounter;
use App\Models\KbCounterBackup;
use App\Models\AdvisoryCounter;
use App\Models\AdvisoryCounterBackup;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
    }
}
