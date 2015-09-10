<?php

namespace Friluft\Http\Controllers\Admin;

use Carbon\Carbon;
use DB;
use Former;
use Friluft\Category;
use Friluft\Coupon;
use Friluft\Product;
use Friluft\Role;
use Illuminate\Http\Request;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Input;
use Response;

class CouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
		$table = \Laratable::make(Coupon::query(), [
			'#' => 'id',
			'Admin Name' => 'admin_name',
			'Name' => 'name',
			'Code' => 'code',
			'Discount' => 'discount',
			'Categories' => ['categories', function($model) {
				$str = '';

				foreach($model->categories as $cat) {
					$cat = Category::find($cat);
					$str .= $cat->name .', ';
				}
				return trim($str, ', ');
			}],
			'Products' => ['products', function($model) {
				$str = '';

				foreach($model->products as $product) {
					$product = Product::find($product);
					$str .= $product->name .', ';
				}

				return trim($str, ', ');
			}],
			'Uses' => ['_uses', function(Coupon $c) {
				return DB::table('coupon_user')->where('coupon_id', '=', $c->id)->count() .' ganger.';
			}],
			'Cumulative' => ['cumulative', function(Coupon $c) {
				return ($c->cumulative) ? '<span class="color-success">Ja.</span>' : '<span class="color-error">Nei.</span>';
			}],
			'Status' => ['valid_until', function(Coupon $c) {
				if ( ! isset($c->valid_until)) return 'N/A';
				if ($c->valid_until->timestamp > time()) {
					return '<span class="color-success">Expires in ' .$c->valid_until->diffForHumans() .'</span>';
				}else {
					return '<span class="color-error">Expired ' .$c->valid_until->diffForHumans() .'</span>';
				}
			}],
			'Enabled' => ['enabled', function(Coupon $c) {
				return ($c->enabled) ? '<span class="color-success">Ja.</span>' : '<span class="color-error">Nei.</span>';
			}],
		]);

		$table->sortable(true, ['admin_name', 'name', 'discount', 'products', 'categories', 'created_at']);
		$table->paginate(true, 20);

		$table->editable(true, url('admin/coupons/{id}/edit'));
		$table->destroyable(true, url('admin/coupons/{id}'));

		return view('admin.coupons_index')->with([
			'table' => $table->render(),
			'pagination' => $table->paginator->render(),
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
		return view('admin.coupons_create')->with([
			'categories' => Category::all(['id', 'name']),
			'products' => Product::all(['id', 'name']),
			'roles' => Role::all(['id', 'name']),
		]);
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Requests\admin\CreateCouponRequest $request
	 * @return Response
	 */
    public function store(Requests\admin\CreateCouponRequest $request)
    {
        $coupon = new Coupon(Input::only(['admin_name', 'name', 'code', 'discount', 'enabled', 'products', 'categories', 'cumulative', 'roles']));

		$coupon->enabled = Input::has('enabled');
		$coupon->cumulative = Input::has('cumulative');
		$coupon->single_use = Input::has('single_use');

		# deactivate automatically?
		if (Input::has('automatic_deactivation')) {
			$coupon->valid_until = Input::get('valid_until');
		}

		$coupon->save();
		return \Redirect::route('admin.coupons.index')->with('success', 'Coupon created!');
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Requests\admin\EditCouponRequest $request
	 * @param Coupon $c
	 * @return Response
	 * @internal param int $id
	 */
    public function edit(Coupon $c)
    {
		$data = $c->toArray();
		if (isset($c->valid_until)) {
			$data['valid_until'] = $c->valid_until->format('Y-m-d');
			$data['automatic_deactivation'] = true;
		}
		Former::populate($data);

		return view('admin.coupons_edit')->with([
			'categories' => Category::all(['id', 'name']),
			'products' => Product::all(['id', 'name']),
			'roles' => Role::all(['id', 'name']),
			'coupon' => $c,
		]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Requests\admin\EditCouponRequest $request, Coupon $c)
    {
        $c->fill(Input::only('admin_name', 'name', 'code', 'discount', 'enabled', 'products', 'categories', 'cumulative', 'roles'));

		$c->enabled = Input::has('enabled');
		$c->cumulative = Input::has('cumulative');
		$c->single_use = Input::has('single_use');

		# deactivate automatically?
		if (Input::has('automatic_deactivation')) {
			list($year, $month, $day) = explode('-', Input::get('valid_until'));
			$c->valid_until = Carbon::createFromDate($year, $month, $day)->setTime(0,0,0);
		}else {
			$c->valid_until = null;
		}

		$c->save();

		return \Redirect::route('admin.coupons.index')->with('success', 'Coupon code updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Coupon $c)
    {
		$c->delete();
		return \Redirect::back()->with('success', 'Coupon code deleted.');
    }
}
