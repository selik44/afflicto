<?php namespace Friluft\Jobs;

use Friluft\Order;
use Friluft\OrderEvent;
use Illuminate\Contracts\Bus\SelfHandling;
use KlarnaFlags;

/**
 * Activates an order in klarna and creates a new OrderEvent describing that.
 * @package Friluft\Commands
 */
class ActivateOrder extends Command implements SelfHandling {

    private $klarna;

    private $order;

	public function __construct(\Klarna $klarna, Order $order)
	{
        $this->klarna = $klarna;
        $this->order = $order;
	}

    /**
     * Handles the command.
     * @return array ['status' => 'x', 'invoice' => '...']
     * @throws \Exception if klarna failed to activate the order
     */
	public function handle()
	{
        try {
            $result = $this->klarna->activate($this->order->reservation, null, KlarnaFlags::RSRV_SEND_BY_EMAIL);

			$this->order->activated = true;
			$this->order->save();

            $orderEvent = new OrderEvent();
            $orderEvent->comment = "Activated";

            $this->order->orderEvents()->save($orderEvent);

            return [
                'status' => $result[0],
                'invoice' => $result[1],
            ];
        }catch (\Exception $e) {
            throw new \Exception("Cannot activate order: " .$e->getMessage());
        }
	}

}