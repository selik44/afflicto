<?php namespace Friluft\Commands;

use Friluft\Order;
use Friluft\OrderEvent;
use Illuminate\Contracts\Bus\SelfHandling;
use KlarnaFlags;

class ActivateOrder extends Command implements SelfHandling {

    private $klarna;

    private $order;

	/**
	 * Create a new command instance.
	 */
	public function __construct(\Klarna $klarna, Order $order)
	{
        $this->klarna = $klarna;
        $this->order = $order;
	}

    /**
     * Execute the command.
     * @return array ['status' => 'x', 'invoice' => '...']
     * @throws \Exception if klarna failed to activate the order
     */
	public function handle()
	{
        try {
            $result = $this->klarna->activate($this->order->reservation, null, KlarnaFlags::RSRV_SEND_BY_EMAIL);

            $orderEvent = new OrderEvent();
            $orderEvent->comment = "activated";

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