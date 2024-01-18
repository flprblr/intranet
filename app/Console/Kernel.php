<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Config;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $integration_mode = Config::get('app.INTEGRATION_MODE');

        if ($integration_mode === 'PRD') {

            // E-COMMERCE ORDERS LIVE
            $schedule->command('get:woocommerce')->everyMinute()->withoutOverlapping(60)
                ->then(function () {
                    $this->call('post:woocommerce');
                })
                ->then(function () {
                    $this->call('post:sovos');
                })
                ->then(function () {
                    $this->call('get:sovos');
                })
                ->then(function () {
                    $this->call('post:pos');
                });

            // SALES LIVE
            $schedule->command('get:sales:day')->everyMinute()->withoutOverlapping(60);

            // MONITORS LIVE
            $schedule->command('monitor:queued:invoices')->everyTenMinutes()->withoutOverlapping(60);

            // FTP DAILY
            $schedule->command('ftp:adidas')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:apple')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:converse')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:followup')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:nike')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:puma')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:reebok')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:shoppertrack')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:reebokinv')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:gfk')->dailyAt('00:15')->withoutOverlapping(60)->runInBackground();
            $schedule->command('ftp:sync')->dailyAt('01:00')->withoutOverlapping(60)->runInBackground();

            // SALES DAILY
            $schedule->command('get:sales:month')->dailyAt('02:00')->withoutOverlapping(60);

            // E-COMMERCE STOCK DAILY
            $schedule->command('get:products')->dailyAt('04:00')->withoutOverlapping(60)
                ->then(function () {
                    $this->call('get:stock');
                })
                ->then(function () {
                    $this->call('post:products');
                });

        } else {
            // MARKETPLACE ORDERS 
            $schedule->command('get:mvorders')->everyFifteenMinutes()->withoutOverlapping(60)
                ->then(function () {
                    $this->call('post:mvsovos');
                })
                ->then(function () {
                    $this->call('get:mvsovos');
                })
                ->then(function () {
                    $this->call('post:mvpos');
                })
                ->then(function () {
                    $this->call('post:mvsap');
                })
                ->then(function () {
                    $this->call('post:mvdocument');
                });
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
