<?php

namespace Friluft\Http\Controllers\Admin;

use Carbon\Carbon;
use Friluft\Category;
use Friluft\Order;
use Friluft\Product;
use Friluft\User;
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

	public function users() {
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
		$users = User::where('created_at', '>=', $min->format(Carbon::ISO8601))->where('created_at', '<=', $max->format(Carbon::ISO8601))->get();

		return view('admin.reports_users')->with([
			'users' => $users,
			'from' => $from->format('Y-m-d'),
			'to' => $to->format('Y-m-d'),
		]);
	}

	public function products() {
		$category = Input::get('category');

		if ($category == '*') {
			$products = Product::where('sales', '>', '0')->orderBy('sales', 'desc')->get();
		}else {
			$products = Product::where('sales', '>', '0')->where('categories', 'LIKE', '%,' .$category .',%')->orderBy('sales', 'desc')->get();
		}

		return view('admin.reports_products')->with([
			'products' => $products,
			'categories' => Category::all(),
		]);
	}

}
