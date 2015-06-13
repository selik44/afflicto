<?php namespace Friluft\Http\Controllers\Admin;

use Exception;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Product;
use Friluft\Order;
use Friluft\Variant;
use Illuminate\Support\Facades\Redirect;
use Klarna;
use Laratable;

class OrdersController extends Controller {

	private $klarna;

	public function __construct() {
		$this->klarna = new Klarna();
		$this->klarna->config(
			env('KLARNA_MERCHANT_ID'),
			env('KLARNA_SHARED_SECRET'),
			\KlarnaCountry::NO,
			\KlarnaCurrency::NOK,
			Klarna::BETA,
			'json',
			base_path('resources/pclasses.json')
		);
	}

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

							# get the first variant value and ID
							$variant = array_values($variants)[0];
							$variantID = array_search($variant, $variants);

							# get the variant model
							$variantModel = Variant::find($variantID);

							# got it?
							if ($variantModel) {
								# set stock and name
								$stock = $variantModel->data['values'][$variant]['stock'];
								$name = $name .' [' .$variant .']';
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
			'' => ['items', function($model) {
				return '<div class="button-group actions">
					<a class="button small primary" title="Details" href="' .route('admin.orders.edit', $model) .'"><i class="fa fa-search"></i></a>
					<form method="POST" action="' .route('admin.orders.delete', $model) .'">
						<input type="hidden" name="_method" value="DELETE">
						<input type="hidden" name="_token" value="' .csrf_token() .'">
						<button title="Delete" class="error small"><i class="fa fa-trash"></i></button>
					</form>
				</div>';
			}],
		]);

		#$table->editable(true, url('admin/orders/{id}/edit'));
		#$table->destroyable(true, url('admin/orders/{id}'));
		$table->selectable(true);
		$table->sortable(true, [
			'status','updated_at','user',
		]);

		return $this->view('admin.orders_index')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
			]);
	}

	public function activate(Order $order) {
		try {
			$this->klarna->activate($order->reservation);
		} catch (\KlarnaException $e) {
			return \Redirect::route('admin.orders.index')->with('error', 'Klarna could not activate the order: ' .$e->getMessage());
		}

		return \Redirect::back()->with('success', 'Order Activated');
	}

	public function create()
	{
		return view('admin.orders_create');
	}

	public function store()
	{

	}

	public function edit(Order $order)
	{
		$items = [];
		foreach($order->items as $item) {
			if ($item['type'] == 'shipping_fee') continue;
			$item['model'] = Product::find($item['reference']['id']);
			$items[] = $item;
		}

		return view('admin.orders_edit')
			->with([
				'order' => $order,
				'items' => $items,
			]);
	}

	public function update(Order $order)
	{
		$order->status = \Input::get('status');
		$order->save();
		return Redirect::back()->with('success', 'Order updated.');
	}

	public function packlist(Order $order) {
		$items = [];
		foreach($order->items as $item) {
			if ($item['type'] == 'shipping_fee') continue;
			$item['model'] = Product::find($item['reference']['id']);
			$items[] = $item;
		}

		return view('admin.orders_packlist')
			->with([
				'order' => $order,
				'items' => $items,
			]);
	}

	public function destroy(Order $order)
	{
		$order->delete();
		return Redirect::back()->with('success', 'Order #' .$order->id .' deleted.');
	}

}
