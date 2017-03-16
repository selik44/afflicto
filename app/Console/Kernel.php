<?php namespace Friluft\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Mail;
use Friluft\Order;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'Friluft\Console\Commands\Inspire',
		'Friluft\Console\Commands\Import',
		'Friluft\Console\Commands\GenerateProductSummary',
		'Friluft\Console\Commands\GenerateThumbnails',
		'Friluft\Console\Commands\ImportImages',
		'Friluft\Console\Commands\ImportUsers',
		'Friluft\Console\Commands\WelcomeUsers',
		'Friluft\Console\Commands\SaveProfit',
		'Friluft\Console\Commands\NukeDatabase',
        'Friluft\Console\Commands\ReviewNotification'
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Sc hedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->daily();

		$schedule->command('reviewnotification')->everyMinute();




//        $schedule->call(function () {
//
//
//            $orders = Order::orders();
//
//            foreach($orders as $order) {
//
//                Mail::send('emails.store.suggest_feedback', $order, function ($mail) {
//                    $mail->to('dudselik44@gmail.com')->subject('Ordrebekreftelse #');
//
//                });
//
//            }
//        })->everyMinute();

	}

}
