<?php

namespace Friluft\Http\Controllers\Admin;

use Carbon\Carbon;
use Friluft\Order;
use Illuminate\Http\Request;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Input;

class ReportsController extends Controller
{

	public function profit() {
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

		$min = $from->copy()->setTime(0, 0, 0);
		$max = $to->copy()->setTime(23, 59, 59);
		$orders = Order::where('created_at', '>=', $min->format(Carbon::ISO8601))->where('created_at', '<=', $max->format(Carbon::ISO8601))->get();

		$profit = 0;
		foreach($orders as $order) {
			$profit += $order->getProfit();
		}

		return view('admin.reports_profit')->with([
			'orders' => $orders,
			'profit' => $profit,
			'from' => $from->format('Y-m-d'),
			'to' => $to->format('Y-m-d'),
		]);
	}

}
