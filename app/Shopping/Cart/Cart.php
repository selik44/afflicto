<?php namespace Friluft\Shopping\Cart;

use Auth;
use DB;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Friluft\Coupon;
use Friluft\User;
use Illuminate\Session\SessionManager;
use Friluft\Product;
use Friluft\Variant;
use Illuminate\Session\Store;
use Klarna_Checkout_Order;
use Klarna_Checkout_Connector;
use Agent;

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
	 * @var Store
	 */
	private $session;

	/**
	 * Creates a new cart instance to manage the shopping cart. Cart contents are stored in the session.
	 * @param \Illuminate\Session\Store So we can store our cart between requests
	 */
	public function __construct(Store $session) {
		$this->session = $session;

		# make sure we have a shopping cart in the session
		if ( ! $this->session->has('shoppingcart')) $this->session->put('shoppingcart', ['contents' => [], 'uid' => 0, 'coupons' => []]);

		if ( ! $this->session->has('shoppingcart.coupons')) $this->session->set('shoppingcart.coupons', []);

		# setup klarna
		Klarna_Checkout_Order::$baseUri = getenv('KLARNA_URI');
		Klarna_Checkout_Order::$contentType = getenv('KLARNA_CONTENT_TYPE');

		# create our klarna checkout connector using the shared secret.
		$this->klarnaConnector = Klarna_Checkout_Connector::create(getenv('KLARNA_SHARED_SECRET'));

		# verify cart contents and remove invalid items
		foreach($this->getItems() as $item) {
			$product = Product::find($item['product_id']);

			# remove it if it's disabled or nonexistent.
			if ( ! $product || ! $product->enabled) {
				$this->remove($item['id']);
				continue;
			}

			# this product requires variants
			if ($product->variants->count() > 0) {

				# no variants specified?
				if ( ! isset($item['options']['variants'])) {
					$this->remove($item['id']);
					continue;
				}

				# verify that the item has a valid option for each of the variants this product has
				foreach ($product->variants as $variant) {
					# not set?
					if ( ! isset($item['options']['variants'][$variant->id])) {
						$this->remove($item['id']);
						continue;
					}

					# is the selected variant value not valid?
					$selectedValue = $item['options']['variants'][$variant->id];

					$isValid = false;
					foreach($variant->data['values'] as $value) {
						if ($value['id'] == $selectedValue) {
							$isValid = true;
						}
					}

					if ( ! $isValid) {
						$this->remove($item['id']);
						continue;
					}
				}
			}
		}

		# verify coupon codes

		$coupons = $this->session->get('shoppingcart.coupons');
		foreach($coupons as $key => $code) {
			if ( ! $this->canUseCoupon($code)) {
				unset($coupons[$key]);
			}
		}
	}

	/**
	 * Get all the items in the cart.
	 * @return Array an array of items. [['id' => 0, 'product_id' => 0, 'quantity' => 1],...]
	 */
	public function getItems() {
		return $this->session->get('shoppingcart.contents');
	}

	public function quantity() {
		$q = 0;
		foreach($this->getItems() as $item) {
			$q += $item['quantity'];
		}
		return $q;
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
				$model = $item['model'];
				$item['model'] = $item['model']->toArray();
				$item['model']['vatgroup'] = $model->vatgroup->toArray();
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

	public function has($uid) {
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
	 * Find an item in the cart that has the given product_id and options.
	 *
	 * @param $product_id id of the product.
	 * @param array $options array of options, like ['variants' => [...], ...]
	 * @return array|null
	 */
	public function getItemLike($product_id, $options = []) {
		foreach($this->getItems() as $item) {
			if ($item['product_id'] == $product_id) {
				if (isset($item['options']['variants']) && isset($options['variants'])) {
					foreach($options['variants'] as $key => $value) {
						if ($item['options']['variants'][$key] != $value) {
							return null;
						}
					}
					return $item;
				}
			}
		}

		return null;
	}

	/**
	 * Add an item to the cart.
	 * @param \Friluft\Product|int|integer a product model instance or integer product_id
	 * @param int|integer The quantity of this item in the cart.
	 * @return int|integer the item uid, used to reference the items in the cart.
	 */
	public function add($product, $quantity = 1, $options = []) {
		# get product
		if (is_object($product) && $product instanceof Product) {
			$product = (int) $product->id;
		}

		$duplicate = $this->getItemLike($product, $options);
		if ($duplicate) {
			$this->setQuantity($duplicate['id'], (int) $duplicate['quantity'] + $quantity);
			return (int) $duplicate['id'];
		}

		# get uid and increment it
		$uid = (int) $this->session->get('shoppingcart.uid') + 1;
		$this->session->put('shoppingcart.uid', $uid);

		# store
		$this->session->put('shoppingcart.contents.' .$uid, [
			'product_id' => $product,
			'id' => $uid,
			'quantity' => (int) $quantity,
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
	 * Removes all the items in the cart. Also resets the UID back to 0 and removes any coupons.
	 * @return void
	 */
	public function clear() {
		$this->session->set('shoppingcart.contents', []);
		$this->session->set('shoppingcart.uid', 0);
		$this->session->set('shoppingcart.coupons', []);
		$this->session->forget('klarna_order');
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
				return $model->getDiscountPrice() * $model->vatgroup->amount * $item['quantity'];
			}
		}

		return 0;
	}

	/**
	 * Get the total price of all contents in the cart. Honors the quantity parameter.
	 * @return float the total price.
	 */
	public function getTotal() {
		$total = 0;
		foreach($this->getItems() as $item) {
			$total += $this->getSubTotal($item['id']);
		}
		return $total;
	}

	public function getTotalWeight() {
		$items = $this->getItemsWithModels(false);
		$weight = 0;

		foreach($items as $item) {
			$weight += $item['model']->weight * $item['quantity'];
		}

		return $weight;
	}

	public function getShipping() {
		$order = $this->getKlarnaOrderData();
		foreach($order['cart']['items'] as $key => $item) {
			if ($item['reference'] == 'SHIPPING') {
				return $item;
			}
		}

		throw new \Exception("No Shipping Data!");
	}

	/**
	 * Get the total amount of tax for all items, including shipping.
	 *
	 * @return float
	 */
	public function getTotalTax() {
		$tax = 0.0;
		foreach($this->getItemsWithModels() as $item) {
			$model = $item['model'];
			$incTax = $model->getDiscountPrice() * $model->vatgroup->amount;
			$tax += $incTax - $model->getDiscountPrice();
		}

		/*$shipping = $this->getShipping();
		$tax += $shipping['total_price_including_tax'];*/

		return $tax;
	}

	/**
	 * Returns the total revenue this order would provide.
	 *
	 * @return float
	 */
	public function getRevenue() {
		$revenue = 0.0;

		foreach($this->getItemsWithModels() as $item) {
			$model = $item['model'];
			$revenue += $model->price - $model->inprice;
		}

		return $revenue;
	}

	/**
	 * Check whether the given coupon is relevant for a specific product.
	 *
	 * @param Product $product
	 * @param Coupon $coupon
	 * @return bool
	 */
	private function isCouponRelevantForProduct(Coupon $coupon, Product $product) {
		# check if this product is in the coupon specifically...
		if (in_array($product->id, $coupon->products)) {
			return true;
		}
		else {
			# check the categories...
			foreach($product->categories as $category) {
				if (in_array($category->id, $coupon->categories)) {
					return true;
				}else {
					# check parents
					foreach($category->parents() as $cat) {
						if (in_array($cat->id, $coupon->categories)) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	private function getBestCouponForProduct(Product $product, array $coupons) {
		$best = null;

		foreach($coupons as $coupon) {
			# make sure this coupon is relevant for this product
			if ( ! $this->isCouponRelevantForProduct($coupon, $product)) continue;

			if ($best == null || $coupon->discount > $best->discount) {
				$best = $coupon;
			}
		}

		return $best;
	}

	private function getBestNonCumulativeCouponForProduct(Product $product, array $coupons) {
		$best = null;

		foreach($coupons as $coupon) {
			# skip if cumulative
			if ($coupon->cumulative) continue;

			# make sure this coupon is relevant for this product
			if ( ! $this->isCouponRelevantForProduct($coupon, $product)) continue;

			if ($best == null || $coupon->discount > $best->discount) {
				$best = $coupon;
			}
		}

		return $best;
	}

	public function getBestCumulativeCouponForProduct(Product $product, $coupons) {
		$best = null;

		foreach($coupons as $coupon) {
			# skip if not cumulative
			if ( ! $coupon->cumulative) continue;

			# make sure this coupon is relevant for this product
			if ( ! $this->isCouponRelevantForProduct($coupon, $product)) continue;

			if ($best == null || $coupon->discount > $best->discount) {
				$best = $coupon;
			}
		}

		return $best;
	}

	public function getKlarnaOrderData() {
		$data = ['cart' => ['items' => []]];

		# get all coupons
		$coupons = $this->getCoupons();

		# Add the products
		foreach($this->getItemsWithModels(false) as $item) {
			$product = $item['model'];

			$discount = $item['model']->getDiscount();
			$coupon = null;

			# we don't have a discount.
			if ($discount == 0) {
				# just get the best coupon
				$bestCoupon = $this->getBestCouponForProduct($product, $coupons);

				if ($bestCoupon != null) {
					# simply apply it
					$discount = $bestCoupon->discount;
					$coupon = $bestCoupon;
				}
			}else {
				# get the best cumulative coupon for this product.
				$cumulative = $this->getBestCumulativeCouponForProduct($product, $coupons);
				$nonCumulative = $this->getBestNonCumulativeCouponForProduct($product, $coupons);

				# got both.. which is best?
				if ($cumulative != null && $nonCumulative != null) {
					# the cumulative is better?
					if ($discount + $cumulative->discount >= $nonCumulative->discount) {
						$coupon = $cumulative;
						$discount = $discount + $cumulative->discount;
					}else if($nonCumulative->discount > $discount) {
						$coupon = $nonCumulative;
						$discount = $nonCumulative->discount;
					}
				}else if (isset($nonCumulative)) {
					# is it better?
					if ($nonCumulative->discount > $discount) {
						$coupon = $nonCumulative;
						$discount = $nonCumulative->discount;
					}
				}else if (isset($cumulative)) {
					$discount = $discount + $cumulative->discount;
					$coupon = $cumulative;
				}
			}

			$data['cart']['items'][] = [
				'reference' => json_encode(['id' => $item['model']->id, 'options' => $item['options']]),
				'name' => $item['model']->name,
				'quantity' => $item['quantity'],
				'unit_price' => ($item['model']->price * $item['model']->vatgroup->amount) * 100,
				'discount_rate' => (int) ($discount * 100),
				'tax_rate' => ($item['model']->vatgroup->amount - 1) * 10000,
			];
		}

		# add shipping costs
		$weight = $this->getTotalWeight();
		$total = $this->getTotal();

		# determine shipping type (mail or service-pack)
		if ($weight < 1000) {
			$shippingType = 'mail';
			$shippingFee = 3120;
		}else {
			$shippingType = 'service-pack';
			$shippingFee = 7920;
		}

		# free shipping?
		if ($total >= 800) {
			$shippingFee = 0;
		}

		# is the cart composed of items with the free shipping tag only?
		$freeShipping = true;
		foreach($this->getItemsWithModels(false) as $item) {
			$hasFreeShipping = false;
			foreach($item['model']->tags as $tag) {
				if ($tag->type == 'free_shipping') {
					$hasFreeShipping = true;
				}
			}
			if ($hasFreeShipping == false) {
				$freeShipping = false;
				break;
			}
		}
		if ($freeShipping) $shippingFee = 0;

		# free shipping from coupon?
		foreach($coupons as $coupon) {
			if ($coupon->free_shipping) {
				$shippingFee = 0;
			}
		}

		$data['cart']['items'][] = [
			'type' => 'shipping_fee',
			'reference' => 'SHIPPING',
			'name' => $shippingType,
			'quantity' => 1,
			'unit_price' => $shippingFee * 1.25,
			'tax_rate' => 2500,
		];

		# set color options
		$data['options']['color_button'] = '#03a1a9';
		$data['options']['color_button_text'] = '#ffffff';
		$data['options']['color_checkbox'] = '#03a1a9';
		$data['options']['color_checkbox_checkmark'] = '#ffffff';
		$data['options']['color_header'] = '#000000';
		$data['options']['color_link'] = '#03a1a9';

		# set shipping info
		$data['options']['shipping_details'] = trans('store.shipping.' .$shippingType);

		# set coupon info?
		if ($this->numCoupons() > 0 ) {
			# get custom
			if ( ! isset($data['merchant_reference'])) {
				$data['merchant_reference'] = [];
				$custom = [];
			}else {
				$custom = json_decode($data['merchant_reference']['orderid2'], true);
			}

			# set coupon
			$custom['coupons'] = $this->session->get('shoppingcart.coupons');

			# save custom again
			$data['merchant_reference']['orderid2'] = json_encode($custom);
		}

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
			'checkout_uri' => url('checkout'),
			'confirmation_uri' => url('success') .'?klarna_order={checkout.order.uri}',
			'push_uri' => url('push') .'?klarna_order={checkout.order.uri}',
		];

		# ---custom data---
		$custom = [];

		# prefill customer data
		$user = \Auth::user();
		if ($user) {
			$data['shipping_address']['email'] = $user->email;
			if ($user->shipping_address && isset($user->shipping_address['postal_code'])) {
				$data['shipping_address']['postal_code'] = $user->shipping_address['postal_code'];
			}
			$custom['user_id'] = $user->id;
		}

		# ---custom data---
		$data['merchant_reference'] = ['orderid2' => json_encode($custom)];

		$order->create($data);

		$this->session->put('klarna_order', $order->getLocation());

		return $order;
	}

	public function canUseCoupon($code) {
		# find an enabled code that matches the given code, is enabled and is still valid.
		$coupon = Coupon::where('code', '=', $code)->where('enabled', '=', '1')->first();

		# found it?
		if ( ! $coupon) return false;

		# is it expired?
		if ($coupon->valid_until != null && $coupon->valid_until->timestamp < time()) return false;

		# are we logged in?
		if ( ! Auth::user()) return false;

		# have we used it already?
		$user = Auth::user();
		if (DB::table('coupon_user')->where('user_id', '=', $user->id)->where('coupon_id', '=', $coupon->id)->count() > 0) {
			return false;
		}

		return true;
	}

	/**
	 * Add coupon by code.
	 *
	 * @param string $code the coupon code
	 * @return bool true on success, false otherwise.
	 */
	public function addCoupon($code) {
		# is it already in the coupons list?
		if ($this->hasCoupon($code)) return false;

		# can we use it?
		if ($this->canUseCoupon($code)) {
			# add the coupon
			$this->session->push('shoppingcart.coupons', $code);

			# update the order in klarna
			$this->updateKlarnaOrder();

			return true;
		}

		return false;
	}

	/**
	 * Get the number of coupons currently in the cart.
	 *
	 * @return int
	 */
	public function numCoupons() {
		return count($this->session->get('shoppingcart.coupons'));
	}

	/**
	 * Check whether the given coupon code is in the cart.
	 */
	public function hasCoupon($code) {
		foreach($this->session->get('shoppingcart.coupons') as $c) {
			if ($code == $c) return true;
		}
		return false;
	}

	/**
	 * Get coupon model by code
	 *
	 * @param $code
	 * @return mixed|null
	 */
	public function getCoupon($code) {
		if ($this->hasCoupon($code)) return Coupon::whereCode($code)->first();
		return null;
	}

	/**
	 * Get array of coupon models. The keys of the array contains the code while the values hold the model instances.
	 */
	public function getCoupons() {
		$coupons = [];

		foreach($this->session->get('shoppingcart.coupons') as $code) {
			$coupons[$code] = Coupon::whereCode($code)->first();
		}

		return $coupons;
	}

}