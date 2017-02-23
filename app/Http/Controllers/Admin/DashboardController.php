<?php namespace Friluft\Http\Controllers\Admin;

use Carbon\Carbon;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Friluft\Order;
use Friluft\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Input;

class DashboardController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		#####################
		# Profit
		#####################
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
			$to = Carbon::now();
		}

		$labels = [];
		$values = [];
		$totalProfit = 0;

		# loop through the days of the interval
		for($i = $from->copy(); $i->timestamp < $to->timestamp; $i->addDay()) {
			$min = $i->copy()->setTime(0, 0, 0);
			$max = $i->copy()->setTime(23, 59, 59);

			# add a label for this date
			$labels[] = $i->toDateString();

			# calculate the total profit for this day
			$profit = 0;
			#dd(Order::where('created_at', '>=', $min->timestamp)->where('created_at', '<=', $max->format(Carbon::ISO8601))->toSql());
			foreach(Order::where('created_at', '>=', $min->format(Carbon::ISO8601))->where('created_at', '<=', $max->format(Carbon::ISO8601))->get() as $order) {
				# get profit
				foreach($order->items as $item) {
					if ( ! isset($item['reference']) || $item['type'] !== 'physical') continue;
					$product = Product::withTrashed()->find($item['reference']['id']);
					if ( ! $product) {
						continue;
					}
					$profit += $item['total_price_excluding_tax'] - $product->inprice * $item['quantity'];
				}
			}

			$values[] = $profit;
			$totalProfit += $profit;
		}

		$stock = new Collection();

		# find products which are not in stock
		$stockTreshold = 3;
		foreach(Product::all() as $product) {
			if ($product->hasVariants()) {
			}else {
				if ($product->stock <= $stockTreshold) {
					$stock[] = ['stock' => $product->stock, 'product' => $product];
				}
			}
		}

		$stock = $stock->sort(function($a, $b) {
			if ($a['stock'] == $b['stock']) return 0;

			if ($a['stock'] > $b['stock']) return 1;

			return -1;
		});

		return $this->view('admin.dashboard')->with([
			'values' => json_encode($values),
			'labels' => json_encode($labels),
			'profit' => $totalProfit,
			'from' => $from->format('Y-m-d'),
			'to' => $to->format('Y-m-d'),
			'stock' => $stock,
		]);
	}

}
