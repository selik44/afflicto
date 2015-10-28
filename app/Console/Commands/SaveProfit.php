<?php

namespace Friluft\Console\Commands;

use Friluft\Order;
use Illuminate\Console\Command;

class SaveProfit extends Command
{

	protected $name = 'saveprofit';

	protected $description = 'Save profit on orders.';

    public function handle()
    {
		foreach(Order::all() as $order) {
			$order->save();
		}
    }
}