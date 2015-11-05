<?php

use Friluft\Order;
use Friluft\Variant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersFixVariantsFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    return;
		foreach(Order::all() as $order) {
			$items = $order->items;
			foreach($items as &$item) {
				if ( ! isset($item['reference']['options']['variants'])) continue;

				foreach($item['reference']['options']['variants'] as $variantID => &$valueName) {
					$variant = Variant::find($variantID);
					echo 'checking ' .$variantID .' => ' .$valueName ."\n";

					foreach($variant->data['values'] as $val) {
						if ($val['name'] == $valueName) {
							$valueName = $val['id'];
							echo 'valueName is ' .$val['id'] ."\n\n";
							break;
						}
					}
				}
			}

			$order->items = $items;
			$order->save();
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		foreach(Order::all() as $order) {
			$items = $order->items;

			foreach($items as &$item) {
				if ( ! isset($item['reference']['options']['variants'])) continue;

				foreach($item['reference']['options']['variants'] as $variantID => &$valueID) {
					$variant = Variant::find($variantID);
					$valueID = $variant->getValueName($valueID);
				}
			}

			$order->items = $items;
			$order->save();
		}
    }
}
