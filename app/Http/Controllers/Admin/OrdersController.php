<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Product;
use Friluft\Order;
use Friluft\Variant;
use Illuminate\Support\Facades\Redirect;
use Laratable;

class OrdersController extends Controller {

	public function index()
	{
		$table = Laratable::make(Order::query(), [
			'#' => 'id',
			'User' => 'user->name',
			'Items' => ['items', function($model, $column, $value) {
				$str = '<ul class="items">';
				foreach($model->items as $item) {
					if ($item['type'] != 'shipping_fee') {
						$model = $item['reference']['id'];
						$model = Product::find('id', '=', $model);

						$stock = $model->stock;

						if (count($model->variants) > 0) {
							# get the variation we ordered
							$variantID = $item['options']['variant'][0];
							$variant = Variant::find($variantID);
							if ($variant) {
								$stock = $variant->stock;
							}
						}

						# color the item by stock
						$class = 'color-success';
						if ($stock < $item['quantity']) $class = 'color-error';

						return '<li class="' .$class .'">' .$model->name .' (' .$stock .'/' .$item['quantity'] .' in stock)</li>';
					}
				}
				$str .= '</ul>';
			}],
			'Status' => ['status', function($model, $column, $value) {
				return ($model->status == 'checkout_complete') ? '<span class="color-success">' .$model->status .'</span>' : '<span class="color-error">' .$model->status .'</span>';
			}],
			'Updated' => 'updated_at diffForHumans',
			'Completed' => 'completed_at diffForHumans',
		]);

		$table->editable(true, url('admin/orders/{id}/edit'));
		$table->destroyable(true, url('admin/orders/{id}'));
		$table->sortable(true, [
			'status','updated_at',
		]);

		return $this->view('admin.orders_index')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
			]);
	}

	public function create()
	{

	}

	public function store()
	{

	}

	public function show(Order $order)
	{

	}

	public function edit(Order $order)
	{

	}

	public function update(Order $order)
	{

	}

	public function destroy(Order $order)
	{
		$order->delete();
		return Redirect::back()->with('success', 'Order #' .$order->id .' deleted.');
	}

}
