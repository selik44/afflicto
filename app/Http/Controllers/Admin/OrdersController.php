<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Jobs\ActivateOrder;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\OrderEvent;
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
			env('KLARNA_LIVE', false) ? Klarna::LIVE : Klarna::BETA,
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
						$product = Product::withTrashed()->find($productID);

						if ($product == null) {
							return "Invalid product data";
						}

						# get stock and name
						$stock = $product->stock;
						$name = $product->name;

						$variantString = '';
						if ($product->hasVariants()) {
							# get the variants we ordered
							$variants = $item['reference']['options']['variants'];

							# build the string describing the variants
							if ($product->isCompound()) {
								foreach($product->getChildren() as $child) {
									foreach($child->varaints as $variant) {
										$variantString .= $child->name .' ' .$variant->name .': ' .$variant->getValueName($variants[$variant->id]) .', ';
									}
								}
							}else {
								foreach($product->getVariants() as $variant) {
									$variantString .= $variant->name .': ' .$variant->getValueName($variants[$variant->id]) .', ';
								}
							}

							# get stock
							$stock = $product->getStock($item['reference']['options']);

							# (we want actual, physical stock so increment that)
							$stock++;
						}
						$variantString = rtrim($variantString, ', ');

						if (strlen($variantString) > 0) $variantString = ' [' .$variantString .']';

						# color the item by stock
						$class = 'color-success';

						if ($stock < $item['quantity']) $class = 'color-error';

						$str .= '<li class="' .$class .'">' .$name .$variantString .' (' .$stock .'/' .$item['quantity'] .' in stock)</li>';
					}
				}
				$str .= '</ul>';
				return $str;
			}],
			'Status' => ['status', function($model, $column, $value) {
				return trans('admin.status.' .$model->status);
			}],
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
						<!--<button title="Delete" class="error small"><i class="fa fa-trash"></i></button>-->
					</form>
				</div>';
			}],
		]);

		#$table->editable(true, url('admin/orders/{id}/edit'));
		#$table->destroyable(true, url('admin/orders/{id}'));
		$table->selectable(true);
		$table->sortable(true, [
			'status','updated_at','user','activated','created_at',
		], 'created_at', 'desc');

		$table->filterable(true);


		$users = ['*' => 'All'];
		foreach(User::orderBy('firstname', 'asc')->orderBy('lastname', 'asc')->get() as $user) {
			$users[$user->id] = $user->name;
		}
		$table->addFilter('user', 'select')->setValues($users);


		$status = [
			'*' => 'All',
			'unprocessed' => trans('admin.status.unprocessed'),
			'written_out' => trans('admin.status.written_out'),
			'delivered' => trans('admin.status.delivered'),
			'cancelled' => trans('admin.status.cancelled'),
			'ready_for_sending' => trans('admin.status.ready_for_sending'),
			'processed' => trans('admin.status.processed'),
			'unused' => trans('admin.status.unused'),
		];
		$table->addFilter('status', 'select')->setValues($status);

		$table->paginate(true, 100);

		return $this->view('admin.orders_index')
			->with([
				'table' => $table->render(),
				'filters' => $table->buildFilters()->addClass('inline'),
				'pagination' => $table->paginator->render(),
			]);
	}

	public function create()
	{
		return view('admin.orders_create')
		->with([
			'products' => Product::all(),
			'users' => User::all(),
		]);
	}

	public function store()
	{
		# create a new order
		$order = new Order();

		$items = json_decode(Input::get('items', []), true);

		foreach($items as &$item) {
			$id = $item['reference']['id'];
			$model = Product::find($id);
			$item['name'] = $model->name;
			$item['unit_price'] = $model->price;
			$item['total_price_including_tax'] = ceil($model->price * $model->vatgroup->amount * $item['quantity']);
			$item['total_price_excluding_tax'] = ceil($model->price * $item['quantity']);
			$item['type'] = 'physical';
			$item['discount_rate'] = 0;
			$item['tax_rate'] = (int) abs((1 - $model->vatgroup->amount) * 100);
		}

		$order->items = $items;

		# determine shipping type
		$total = $order->getTotal();
		$weight = $order->getWeight();
		if ($weight <= 1000) {
			$shippingType = 'mail';
			$shippingCost = 39;
		}else {
			$shippingType = 'service-pack';
			$shippingCost = 99;
		}

		# free shipping?
		if ($total >= 800) {
			$shippingCost = 0;
		}

		# add shipping
		$items = $order->items;
		$items[] = [
			'discount_rate' => 0,
			'name' => $shippingType,
			'quantity' => 1,
			'reference' => null,
			'tax_rate' => 0,
			'total_price_excluding_tax' => $shippingCost,
			'total_price_including_tax' => $shippingCost,
			'total_tax_amount' => 0,
			'type' => 'shipping_fee',
			'unit_price' => $shippingCost,
		];
		$order->items = $items;

		# set user
		$order->user_id = Input::get('user_id');

		# set shipping & billing addresses
		$order->billing_address = [
			'given_name' => $order->user->firstname,
			'family_name' => $order->user->lastname,
			'street_address' => null,
			'postal_code' => null,
			'city' => null,
			'country' => null,
			'email' => $order->user->email,
			'phone' => null,
		];
		$order->shipping_address = $order->billing_address;

		# set locale etc
		$order->locale = 'nb-no';
		$order->purchase_country = 'no';
		$order->purchase_currency = 'nok';

		# set total
		$order->total_price_including_tax = $order->getTotal();

		$total = 0;
		foreach($order->items as $item) {
			$total += $item['total_price_excluding_tax'];
		}
		$order->total_price_excluding_tax = $total;

		# total tax amount
		$tax = 0;
		foreach($order->items as $item) {
			$tax += $item['total_price_including_tax'] - $item['total_price_excluding_tax'];
		}
		$order->total_tax_amount = $tax;

		$order->status = 'unprocessed';

		# save
		$order->save();

		# redirect to edit that order now
		return Redirect::to(route('admin.orders.edit', $order->id));
	}

	public function edit(Order $order)
	{
		\Former::populate($order);

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
				'users' => User::all(),
			]);
	}

	public function update(Order $order)
	{
		# set status
		$order->status = Input::get('status');

		# set billing values
		$billing = $order->billing_address;
		$billing['given_name'] = Input::get('billing_name');
		$billing['street_address'] = Input::get('billing_street_address');
		$billing['postal_code'] = Input::get('billing_postal_code');
		$billing['city'] = Input::get('billing_city');
		$billing['country'] = Input::get('billing_country');
		$billing['phone'] = Input::get('billing_phone', '');
		$order->billing_address = $billing;


		# set shipping values
		$shipping = $order->shipping_address;
		$shipping['given_name'] = Input::get('shipping_name');
		$shipping['street_address'] = Input::get('shipping_street_address');
		$shipping['postal_code'] = Input::get('shipping_postal_code');
		$shipping['city'] = Input::get('shipping_city');
		$shipping['country'] = Input::get('shipping_country');
		$shipping['phone'] = Input::get('shipping_phone', '');
		$order->shipping_address = $shipping;

		# save
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

		# create a message?
		if (Input::has('message')) {
			# create a new orderevent
			$event = new OrderEvent();
			$event->comment = Input::get('message');
			$event->order()->associate($order);
			$event->save();

			# notify user?
			if (Input::has('notify_user')) {
				\Mail::send('emails.store.order_updated', ['event' => $event], function($mail) use($order, $event) {
					$mail->subject(trans('emails.order_updated.subject', ['id' => $order->id]))->to($order->user->email);
				});
			}
		}

		return Redirect::back()->with('success', 'Order updated');
	}

	public function products_edit(Order $order) {
		if ($order->activated) return Response::back()->with('error', 'That order has been activated and cannot be changed!');
		return view('admin.orders_products_edit')->with([
			'order' => $order,
			'products' => Product::all(),
		]);
	}

	public function products_update(Order $order) {
		if ($order->activated) return Response::back()->with('error', 'That order has been activated and cannot be changed!');
		$items = [];
		foreach(Input::get('items', []) as $item) {

			if ($item['type'] == 'shipping_fee') {
				$items[] = $item;
				continue;
			}

			$product = Product::find($item['reference']['id']);

			# get total tax amount
			$total = $product->price * $product->quantity * $product->vatgroup->amount;
			$taxAmount = abs($total - ($total * $product->vatgroup->amount));

			if (!isset($item['reference']['options'])) {
				$item['reference']['options'] = ['variants' => []];
			}

			$items[] = [
				'discount_rate' => 0,
				'name' => $item['name'],
				'quantity' => (int) $item['quantity'],
				'reference' => $item['reference'],
				'type' => 'physical',
				'tax_rate' => abs((1 - $product->vatgroup->amount)),
				'total_price_excluding_tax' => ($product->price * $product->quantity),
				'total_price_including_tax' => ($product->price * $product->quantity) * $product->vatgroup->amount,
				'total_tax_amount' => $taxAmount,
				'unit_price' => $product->price * $product->vatgroup->amount,
			];
		}
		$order->items = $items;

		# set total
		$order->total_price_including_tax = $order->getTotal();

		$total = 0;
		foreach($order->items as $item) {
			$total += $item['total_price_excluding_tax'];
		}
		$order->total_price_excluding_tax = $total;

		# total tax amount
		$tax = 0;
		foreach($order->items as $item) {
			$tax += $item['total_price_including_tax'] - $item['total_price_excluding_tax'];
		}
		$order->total_tax_amount = $tax;

		$order->save();

		return response('OK');
	}

	public function update_status($orders, $status) {
		$orders = explode(',', $orders);
		foreach($orders as $id) {
			$order = Order::find($id);
			if ($order) {

				# activate?
				if ($status == 'ready_for_sending' && strlen($order->klarna_id) > 0) {
					$activate = new ActivateOrder($this->klarna, $order);
					$this->dispatch($activate);
				}else {
					# just update the status
					$order->status = $status;
					$order->save();
				}
			}
		}

		return Redirect::back()->with('success', 'orders updated!');
	}

	public function getMultiPacklist($ordersList) {
		$orders = [];
		foreach(explode(',', $ordersList) as $id) {
			$orders[] = Order::find($id);
		}

		return view('admin.orders_packlist')
			->with([
				'orders' => $orders,
			]);
	}

	/**
	 * Generates a packlist PDF for a single order.
	 *
	 * @param Order $order
	 * @return $this
	 */
	public function packlist(Order $order) {
		return view('admin.orders_packlist')
			->with([
				'orders' => [$order],
			]);
	}

	public function destroy(Order $order)
	{
		foreach($order->orderEvents as $event) {
			$event->delete();
		}

		$order->delete();
		return Redirect::back()->with('success', 'Order #' .$order->id .' deleted.');
	}

}
