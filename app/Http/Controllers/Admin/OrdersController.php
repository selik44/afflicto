<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Commands\ActivateOrder;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Product;
use Friluft\Order;
use Friluft\User;
use Friluft\Variant;
use Illuminate\Support\Facades\Redirect;
use Input;
use Klarna;
use Laratable;
use Snappy;
use Response;
use SplFileInfo;

class OrdersController extends Controller {

	private $klarna;

	public function __construct() {
		$this->klarna = new Klarna();
		$this->klarna->config(
			env('KLARNA_MERCHANT_ID'),
			env('KLARNA_SHARED_SECRET'),
			\KlarnaCountry::NO,
			\KlarnaLanguage::NB,
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
				$str = '<ul class="flat items">';
				foreach($model->items as $item) {
					if ($item['type'] !== 'shipping_fee') {
						# get product ID and model
						$productID = $item['reference']['id'];
						$product = Product::find($productID);

						if ($product == null) {
							return "Invalid product data";
						}

						# get stock and name
						$stock = $product->stock;
						$name = $product->name;

						$variantString = '';
						if (count($product->variants) > 0) {
							# get the variants we ordered
							$variants = $item['reference']['options']['variants'];

							# create the string describing the variants
							$stockID = [];
							foreach($variants as $variantID => $value) {
								$variantModel = Variant::find($variantID);
								$variantString .= $variantModel->name .': ' .$value .', ';
								foreach($variantModel->values as $v) {
									$stockID[$value] = $v['id'];
								}
							}
							$stockID = implode('_', $stockID);
							$stock = $product->variants_stock[$stockID];
						}
						$variantString = rtrim($variantString, ', ');

						if (strlen($variantString) > 0) $variantString = ' [' .$variantString .'] ';

						# color the item by stock
						$class = 'color-success';
						if ($stock < $item['quantity']) $class = 'color-error';

						$str .= '<li class="' .$class .'">' .$name .$variantString .'(' .$stock .'/' .$item['quantity'] .' in stock)</li>';
					}
				}
				$str .= '</ul>';
				return $str;
			}],
			'Status' => 'status',
			'Activated' => ['activated', function($model) {
				if ($model->activated) return '<span class="color-success">Yes</span>';
				return '<span class="color-error">No</span>';
			}],
			'Created' => 'created_at diffForHumans',
			'' => ['_actions', function($model) {
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
			'status','updated_at','user','activated',
		]);

		$table->filterable(true);


		$users = ['*' => 'All'];
		foreach(User::orderBy('firstname', 'asc')->orderBy('lastname', 'asc')->get() as $user) {
			$users[$user->id] = $user->name;
		}
		$table->addFilter('user', 'select')->setValues($users);


		$status = [
			'*' => 'All',
			'unprocessed' => 'Ubehandlet',
			'written_out' => 'Skrevet ut',
			'delivered' => 'Levert',
			'cancelled' => 'Kansellert',
			'ready_for_sending' => 'Klar til Sending',
			'processed' => 'Behandlet',
			'restorder' => 'Restordre',
		];
		$table->addFilter('status', 'select')->setValues($status);

		$table->paginate(true);

		return $this->view('admin.orders_index')
			->with([
				'table' => $table->render(),
				'filters' => $table->buildFilters()->addClass('inline'),
				'pagination' => $table->paginator->render(),
			]);
	}

	public function activate(Order $order) {
		try {
			$this->klarna->activate((int) $order->reservation);
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
		# set status
		$order->status = Input::get('status');
		$order->save();

		# activate?
		if ($order->status == 'ready_for_sending' && ! $order->activated) {
            $activate = new ActivateOrder($this->klarna, $order);
            try {
                $this->dispatch($activate);
            }catch(\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
		}

		return Redirect::back()->with('success', 'Order updated');
	}

	public function products_edit(Order $order) {
		return view('admin.orders_products_edit')->with(['order' => $order]);
	}

	public function products_update(Order $order) {
		$order->save();

		return Redirect::to(route('admin.orders.edit', $order))->with('success', 'Order Updated!');
	}

    /**
     * Generates a multi-page PDF of multiple packlists.
     * @param $orders
     * @return Response
     */
	public function getMultiPacklist($orders) {

		# get orders
		$orders = explode(',', $orders);

		# get packlists for orders in HTML
		$html = [];
		foreach($orders as $id) {
			$html[] = $this->packlist(Order::find($id))->render();
		}

		# make PDF
		$pdf = Snappy::make();

		$filename = storage_path('app/pdf/' .'packlists_' .str_random(16) .'.pdf');

		$pdf->generateFromHtml($html, $filename, [], true);

		# return download response
		return Response::download(new SplFileInfo($filename), "Packlists.pdf");
	}

	/**
	 * Generates a packlist PDF for a single order.
	 *
	 * @param Order $order
	 * @return $this
	 */
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
