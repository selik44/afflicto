<?php namespace Friluft\Shopping\Cart;

use Exception;
use Illuminate\Session\SessionManager;
use Friluft\Product;
use Klarna_Checkout_Order;
use Klarna_Checkout_Connector;

class UnknownProductException extends Exception {}

/**
 * Shopping cart that is saved to the Session.
 */
class Cart {

	/**
	 * The klarna connector instance.
	 * @var Klarna_Checkout_Connector
	 */
	private $klarnaConnector;

	/**
	 * Creates a new cart instance to manage the shopping cart. Cart contents are stored in the session.
	 * @param Illuminate\Session\Store So we can store our cart between requests
	 */
	public function __construct(SessionManager $session) {
		$this->session = $session;

		# make sure we have a shopping cart in the session
		if (!$this->session->has('shoppingcart')) $this->session->put('shoppingcart', ['contents' => [], 'uid' => 0]);

		# setup klarna
		Klarna_Checkout_Order::$baseUri = getenv('KLARNA_URI');
		Klarna_Checkout_Order::$contentType = getenv('KLARNA_CONTENT_TYPE');

		# create our klarna checkout connector using the shared secret.
		$this->klarnaConnector = Klarna_Checkout_Connector::create(getenv('KLARNA_SHARED_SECRET'));
	}

	/**
	 * Get all the items in the cart.
	 * @return Array an array of items. [['id' => 0, 'product_id' => 0, 'quantity' => 1],...]
	 */
	public function getItems() {
		return $this->session->get('shoppingcart.contents');
	}

	public function nothing() {
		return (count($this->getItems()) <= 0);
	}

	public function getItemsWithModels($toArray = false) {
		$items = $this->getItems();
		foreach($items as &$item) {
			$item['model'] = Product::findOrFail($item['product_id']);
			$item['price'] = $item['model']->price;
			$item['subTotal'] = $item['model']->price * $item['quantity'];

			# add some extra fields for convenience
			$item['url'] = url($item['model']->getPath());

			if ($toArray) {
				$item['model'] = $item['model']->toArray();
			}
		}
		return $items;
	}

	/**
	 * Check whether the given item exists, by item UID
	 * @param  int|integer the item UID
	 * @return Array the item array
	 */
	public function exists($uid) {
		return $this->session->has('shoppingcart.contents.' .$uid);
	}

	/**
	 * Get an item by item UID
	 * @param  int|integer the item uid
	 * @return Array|null The item, or null if it doesn't exist.
	 */
	public function get($uid) {
		return $this->session->get('shoppingcart.contents.' .$uid, null);
	}

	/**
	 * Add an item to the cart.
	 * @param Friluft\Product|int|integer a product model instance or integer product_id
	 * @param int|integer The quantity of this item in the cart.
	 * @return int|integer the item uid, used to reference the items in the cart.
	 */
	public function add($product, $quantity = 1, $options = []) {
		# get product
		if (is_object($product) && $product instanceof Product) {
			$product = $product->id;
		}

		# get uid and increment it
		$uid = $this->session->get('shoppingcart.uid') + 1;
		$this->session->put('shoppingcart.uid', $uid);

		# store
		$this->session->put('shoppingcart.contents.' .$uid, [
			'product_id' => $product,
			'id' => $uid,
			'quantity' => $quantity,
			'options' => $options,
		]);

		# return the uid
		return $uid;
	}

	/**
	 * Remove an item from the cart. Returns the removed item.
	 * @param  int|integer the item UID (not product_id)
	 * @return Array|null the item array, or null if it doesn't exist.
	 */
	public function remove($uid) {
		return $this->session->pull('shoppingcart.contents.' .$uid, null);
	}

	/**
	 * Removes all the items in the cart. Also resets the UID back to 0.
	 * @return void
	 */
	public function clear() {
		$this->session->put('shoppingcart.contents', []);
		$this->session->put('shoppingcart.uid', 0);
	}

	/**
	 * Update the quantity of an item. If quantity is 0 or less, removes the item.
	 * @param integer the item UID.
	 * @param integer quantity to set.
	 */
	public function setQuantity($uid, $quantity) {
		$quantity = (int) $quantity;

		if ($this->exists($uid)) {
			if ($quantity <= 0) {
				$this->remove($uid);
			}else {
				$this->session->put('shoppingcart.contents.' .$uid .'.quantity', $quantity);
			}
		}
	}

	public function getProductModel($uid) {
		$item = $this->get($uid);
		return ($item) ? Product::find($item['product_id']) : null;
	}

	/**
	 * Get the total price of an item. (Product price * quantity)
	 * @param  integer the item UID
	 * @return float the product price multiplied by the quantity.
	 */
	public function getSubTotal($uid) {
		$item = $this->get($uid);

		if ($item) {
			$model = $this->getProductModel($uid);

			if ($model) {
				return ($model->price * $model->vatgroup->amount) * $item['quantity'];
			}
		}

		return 0.0;
	}

	/**
	 * Get the total price of all contents in the cart. Honors the quantity parameter.
	 * @return float the total price.
	 */
	public function getTotal() {
		$total = 0.0;
		foreach($this->getItems() as $item) {
			$total += $this->getSubTotal($item['id']);
		}
		return $total;
	}

	public function getTotalWeight() {
		$items = $this->getItemsWithModels(false);
		$weight = 0;

		foreach($items as $item) {
			$weight += $item['model']->weight;
		}

		return $weight;
	}

	public function getKlarnaOrderData() {
		$data = ['cart' => ['items' => []]];

		# Add the products
		foreach($this->getItemsWithModels(false) as $item) {
			$data['cart']['items'][] = [
				'reference' => json_encode(['id' => $item['model']->id, 'options' => $item['options']]),
				'name' => $item['model']->name,
				'quantity' => $item['quantity'],
				'unit_price' => ($item['model']->price * $item['model']->vatgroup->amount) * 100,
				'discount_rate' => 0,
				'tax_rate' => ($item['model']->vatgroup->amount - 1) * 100,
			];
		}

		# add shipping costs
		/*
		$weight = $this->getTotalWeight();
		$total = $this->getTotal();
		$shippingFee = 9900;
		if ($weight < 1000) {
			$shippingFee = 3900;
		}

		$data['cart']['items'][] = [
			'type' => 'shipping_fee',
			'reference' => 'SHIPPING',
			'name' => 'Shipping Fee',
			'quantity' => 1,
			'unit_price' => $shippingFee,
			'tax_rate' => 0,
		];
		*/

		return $data;
	}

	public function updateKlarnaOrder() {
		if ($this->session->has('klarna_order')) {
			try {
				$order = $this->getKlarnaOrder($this->session->get('klarna_order'));
			}catch(Exception $e) {
				$order = $this->getKlarnaOrder();
			}
		}else {
			$order = $this->getKlarnaOrder();
		}

		return $order->update($this->getKlarnaOrderData());
	}

	/*
	 * TODO: implement these color value thingies
	$order['options']['color_button'] = '#04C5CF';
	$order['options']['color_button_text'] = '#FFF';
	$order['options']['color_header'] = '#04C5CF';
	$order['options']['color_link'] = '#04C5CF';
	$order['options']['color_checkbox'] = '#04C5CF';
	$order['options']['color_checkbox_checkmark'] = '#fff';
	*/

	public function getKlarnaOrder($id = null) {
		# get specific order?
		if ($id != null) {
			$order = new Klarna_Checkout_Order($this->klarnaConnector, $id);
			$order->fetch();
			return $order;
		}

		# create klarna order
		$order = new Klarna_Checkout_Order($this->klarnaConnector);
		$data = $this->getKlarnaOrderData();

		# configure checkout
		$data['purchase_country'] = 'NO';
		$data['purchase_currency'] = 'NOK';
		$data['locale'] = 'nb-no';
		$data['merchant'] = [
			'id' => getenv('KLARNA_MERCHANT_ID'),
			'terms_uri' => url('terms-and-conditions'),
			'checkout_uri' => url('store/checkout'),
			'confirmation_uri' => url('store/success') .'?klarna_order={checkout.order.uri}',
			'push_uri' => url('store/push') .'?klarna_order={checkout.order.uri}',
		];

		$order->create($data);

		$this->session->put('klarna_order', $order->getLocation());

		return $order;
	}

}