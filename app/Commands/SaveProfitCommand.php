<?php

namespace Friluft\Commands;

use Friluft\Commands\Command;
use Friluft\Order;
use Illuminate\Contracts\Bus\SelfHandling;

class SaveProfitCommand extends Command implements SelfHandling
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
		foreach(Order::all() as $order) {
			$order->profit = $order->getProfit();
			$order->save();
		}
    }
}
