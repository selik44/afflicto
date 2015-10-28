<?php

namespace Friluft\Http\Controllers\Admin;

use Carbon\Carbon;
use Friluft\Category;
use Friluft\Order;
use Friluft\Product;
use Friluft\User;
use Friluft\Variant;
use Illuminate\Http\Request;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Illuminate\Support\Collection;
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
			$profit += $order->profit;
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
		# get from and to dates
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

		# get categories
		$category = Input::get('category', '*');
		if ($category != '*')
			$categoryModel = Category::find($category);
		else
			$categoryModel = null;

		$products = new Collection();

		foreach(Order::where('created_at', '>=', $min->format(Carbon::ISO8601))->where('created_at', '<=', $max->format(Carbon::ISO8601))->get() as $order) {
			foreach($order->items as $item) {
				if ($item['type'] != 'physical') continue;

				$id = $item['reference']['id'];
				if ( ! $id) throw new \Exception("Error, invalid id: " .$id .' on order id ' .$order->id);

				$model = Product::withTrashed()->find($id);

				if ( ! $model) {
					throw new \Exception("Product model not found for ID: " .$id);
				}

				if ($category != '*') {
					if ( ! $model->categories->contains($categoryModel)) {
						continue;
					}
				}

				# add?
				if ( ! isset($products[$id])) {
					$products[$id] = [
						'product' => $model,
						'quantity' => 0,
						'variants' => [],
					];
				}

				$array = $products[$id];

				$array['quantity'] += $item['quantity'];

				# variants?
				if ($model->hasVariants()) {

					$stockID = [];
					$variantString = [];
					foreach($item['reference']['options']['variants'] as $variantID => $valueID) {
						$variantModel = Variant::find($variantID);
						$variantString[] = $variantModel->name .':' .$variantModel->getValueName($valueID);
						$stockID[] = $valueID;
					}

					$stockID = implode(',', $stockID);

					if ( ! isset($array['variants'][$stockID])) {
						$array['variants'][$stockID] = ['string' => $variantString, 'quantity' => $item['quantity']];
					}else {
						$array['variants'][$stockID]['quantity'] += $item['quantity'];
					}
				}

				$products[$id] = $array;
			}
		}

		# sort by quantity
		$quantitySort = function($a, $b) {
			if ($a['quantity'] == $b['quantity']) return 0;

			if ($a['quantity'] > $b['quantity']) return -1;

			return 1;
		};

		# sort products by quantity
		$products = $products->sort($quantitySort);

		# sort variants by quantity
		foreach($products as $product) {
			$c = new Collection($product['variants']);
			$c = $c->sort($quantitySort);
			$product['variants'] = $c->toArray();
		}

		# return view
		return view('admin.reports_products')->with([
			'products' => $products,
			'categories' => Category::all(),
			'from' => $from->format('Y-m-d'),
			'to' => $to->format('Y-m-d'),
		]);
	}

}
