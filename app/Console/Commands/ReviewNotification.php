<?php namespace Friluft\Console\Commands;

use DB;
use Mail;
use Illuminate\Console\Command;
use Friluft\Order;


class ReviewNotification extends Command {


    protected $name = 'reviewnotification';

    protected $description = 'Display an inspiring quote';



    public function __construct()
    {
        parent::__construct();
    }

    public function handle() {

//        $orders = DB::table('orders')->select();

        $orders = Order::OfferFeedback();

        foreach($orders as $order)
        {

            $email = $order->user->email;

            $send = Order::find($order->id);
            $send->requestReviewTime();


            # notify user
            Mail::send('emails.store.suggest_feedback', ['order' => $order], function($mail) use($email){
                $mail->to($email)->subject('Ordrebekreftelse #');

            });
        }
    }

}
