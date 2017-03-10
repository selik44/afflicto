<?php namespace Friluft\Console\Commands;

use DB;
use Mail;
use Illuminate\Console\Command;


class ReviewNotification extends Command {


    protected $name = 'reviewnotification';

    protected $description = 'Display an inspiring quote';



    public function __construct()
    {
        parent::__construct();
    }

    public function handle() {

        $orders = DB::table('orders')->select();

        foreach($orders as $order)
        {
            # notify user
            Mail::send('emails.store.suggest_feedback', ['order' => 'hi'], function($mail) {
                $mail->to('dudselik44@gmail.com')->subject('Ordrebekreftelse #');

            });
        }
    }

}
