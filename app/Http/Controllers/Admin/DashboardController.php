<?php namespace Friluft\Http\Controllers\Admin;

use Carbon\Carbon;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Order;
use Friluft\Product;
use Illuminate\Http\Request;
use Input;

class DashboardController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (Input::has('from')) {
			list($year, $month, $day) = explode('-', Input::get('from'));
			$from = Carbon::createFromDate($year, $month, $day);
		}else {
			$from = Carbon::now()->subMonth(1)->setTime(0,0,0);
		}

		if (Input::has('to')) {
			list($year, $month, $day) = explode('-', Input::get('to'));
			$to = Carbon::createFromDate($year, $month, $day);
		}else {
			$to = Carbon::now()->subMonth(1)->setTime(0,0,0);
		}

		$labels = [];
		$values = [];

		# loop through the days of the interval
		$current = $from->copy();
		for($i = $from->copy(); $i->timestamp < $to->timestamp; $i->addDay()) {
			$min = $i->copy()->setTime(0, 0, 0);
			$max = $i->copy()->setTime(23, 59, 59);

			# add a label for this date
			$labels[] = $i->toDateString();

			# calculate the total profit for this day
			$profit = 0;
			foreach(Order::where('created_at', '>=', $min->timestamp)->where('created_at', '<=', $max->timestamp)->get() as $order) {
				dd($order)
				# get profit
				foreach($order->items as $item) {
					$product = Product::find($item['reference']['id']);
					$profit += $item['total_price_excluding_tax'] - $product->inprice * $item['quantity'];
				}
			}

			$values[] = $profit;
		}

		if (Input::has('from')) dd(['values' => $values, 'labels' => $labels]);

		return $this->view('admin.dashboard')->with([

		]);
	}

}
