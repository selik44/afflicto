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
						# get product ID and model
						$productID = $item['reference']['id'];
						$product = Product::find($productID);

						# get stock and name
						$stock = $product->stock;
						$name = $product->name;

						if (count($product->variants) > 0) {
							# get the variant we ordered
							$variants = $item['reference']['options']['variants'];

							# get first variant value
							$variant = array_shift($variants);

							# find the ID of this variant
							$variantID = array_search($variant, $variants);

							# get the variant model
							$variantModel = Variant::find($variantID);

							# got it?
							if ($variantModel) {
								# set stock and name
								$stock = $variantModel->data['values'][$variant]['stock'];
								$name = $name .' (' .$variant .')';
							}
						}

						# color the item by stock
						$class = 'color-success';
						if ($stock < $item['quantity']) $class = 'color-error';

						$str .= '<li class="' .$class .'">' .$name .' (' .$stock .'/' .$item['quantity'] .' in stock)</li>';
					}
				}
				$str .= '</ul>';
				return $str;
			}],
			'Status' => ['status', function($model, $column, $value) {
				return ($model->status == 'checkout_complete') ? '<span class="color-success">' .$model->status .'</span>' : '<span class="color-error">' .$model->status .'</span>';
			}],
			'Updated' => 'updated_at diffForHumans',
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
