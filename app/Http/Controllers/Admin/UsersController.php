<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Controllers\Controller;
use Friluft\Http\Controllers\StoreController;
use Friluft\Http\Requests\CreateUserRequest;
use Friluft\Product;
use Friluft\Role;
use Friluft\User;
use Friluft\Review;
use Friluft\Order;
use Friluft\Coupon;
use Illuminate\Support\Facades\Auth;
use Laratable;
use Former;
use Input;
use Redirect;
use Mail;
use Symfony\Component\DomCrawler\Form;

class UsersController extends Controller{

	public function index()
	{
		$table = Laratable::make(User::query()->where('role_id', '!=', Role::where('machine', '=', 'regular')->first()->id), [
			'#' => 'id',
			'Role' => ['role_id', function($model) {
				return '<a href="' .action('Admin\RolesController@edit', ['role' => $model->role->id]) .'">' .$model->role->name .'</a>';
			}],
			'Name' => ['firstname', function($model) {
				return e($model->firstname .' ' .$model->lastname);
			}],
			'Email' => 'email',
			'Registered' => 'created_at diffForHumans',
		]);

		$table->editable(true, url('admin/users/{id}/edit'));
		$table->destroyable(true, url('admin/users/{id}'));
		$table->sortable(true, [
			'firstname','lastname','email','created_at','role_id',
		]);

		$table->filterable(true);

		$table->addFilter('firstname', 'username');

		$table->addFilter('id', 'search')->setLabel('Kundenummer');

		return $this->view('admin.users_index')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
				'filters' => $table->buildFilters()
			]);
	}

	public function customers() {
		$table = Laratable::make(User::query()->where('role_id', '!=', Role::where('machine', '=', 'superadmin')->first()->id), [
			'#' => 'id',
			'Role' => ['role_id', function($model) {
				return '<a href="' .action('Admin\RolesController@edit', ['role' => $model->role->id]) .'">' .$model->role->name .'</a>';
			}],
			'Name' => ['firstname', function($model) {
				return e($model->firstname .' ' .$model->lastname);
			}],
			'Email' => 'email',
			'Registered' => 'created_at diffForHumans',
		]);

		$table->editable(true, url('admin/users/{id}/edit'));
		$table->destroyable(true, url('admin/users/{id}'));
		$table->sortable(true, [
			'firstname','lastname','email','created_at','role_id',
		]);

		$table->filterable(true);

		$table->addFilter('firstname', 'username');

		$table->addFilter('id', 'search')->setLabel('Kundenummer')->fuzzy(false);

		return $this->view('admin.users_index')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
				'filters' => $table->buildFilters()
			]);
	}

	public function create()
	{
		return $this->view('admin.users_create')
			->with([
				'form' => form('admin.user', ['roles' => Role::all()]),
			]);
	}

	public function store(CreateUserRequest $request)
	{
		$user = new User(Input::only('firstname', 'lastname', 'email'));

		$role = Role::find(Input::get('role'));
		if (!$role) $role = Role::where('machine', '=', 'regular')->first();

		# are we setting a role other than 'regular'?
		# if so, check that the user is an admin.
		if ($role->machine !== 'regular') {
			if (Auth::user()->role->machine !== 'admin') {
				# nope!
				return Redirect::back()->withInput()->with('error', @lang('admin.disallowed_role_grant'));
			}
		}

		$user->role()->associate($role);

		$user->save();

		return Redirect::back()->with('success', 'User created!');
	}





	public function show($id)
	{
		return 'Not implemented.';
	}

	public function edit(User $user)
	{
		Former::populate($user);

		return $this->view('admin.users_edit')
			->with([
				'form' =>form('admin.user'),
				'user' => $user,
			]);
	}

	public function update(User $user)
	{
		$user->firstname = Input::get('firstname', $user->firstname);
		$user->lastname = Input::get('lastname', $user->lastname);
		$user->email = Input::get('email', $user->email);

		# are we changing the role?
		$role = Role::find(Input::get('role_id'));
		if (!$role) $role = Role::where('machine', '=', 'regular')->first();
		if ($role->id !== $user->role->id) {

			# are we setting a role other than 'regular'?
			# if so, check that the user is an admin.
			if ($role->machine !== 'regular') {
				if (Auth::user()->role->machine !== 'superadmin') {
					# nope!
					return Redirect::back()->withInput()->with('error', trans('validation.disallowed_role_grant'));
				}
			}
			$user->role()->associate($role);
		}

		$user->save();

		return Redirect::route('admin.users.index')->with('success', e($user->name) .' updated.');

	}

	public function destroy(User $user)
	{

		$user->delete();
		return Redirect::route('admin.users.index')->with('success', e($user->name) .' deleted.');
	}

	public function destroyReview($id)
    {

        $review = Review::where('id', '=', $id)->first();
        Review::where('id', '=', $id)->first()->delete();

	    return Redirect::route('admin.users.reviews')->with('success', e($review->comment) .' deleted.');

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCouponRequest $request
     * @return Response
     */
    public function createCoupone()
    {

        $code = csrf_token();

        $data = array(

            'admin_name' => 'admin',
            'name' => 'admin',
            'code' => $code,
            'discount' => 5,
            'enabled' => 1,
            'cumulative' => 0,
            'roles' => null,
            'single_use' => 1

        );

        $coupon = new Coupon($data);

        $coupon->save();

        return $code;

    }


    public function approveReview($review)
    {

        $review = Review::findOrFail($review);
        $review->approve();


        $requestSend = Order::RequestDate($review->user_id);
        $code = $this->createCoupone();

        if(count($requestSend) > 0){

            foreach ($requestSend as $request){

                $email = $request->user->email;

                $order = Order::findOrFail($request->id);
                $order->couponeActivated();

                #send coupone
                Mail::send('emails.store.feedback_review', ['coupone' => $code], function($mail) use($email){
                    $mail->to($email)->subject('Discount coupone');

                });

            }
        }

        return redirect()->back()->with('review_approved', true);

    }

	public function review(){


	    $review = Review::all();

        $table = Laratable::make(Review::query(), [
            '#' => 'id',
            'user_id' => 'user_id',
            'product_id' => 'product_id',
            'product_name' => ['product_name', function($model) {

                return  Product::where('id', '=', $model->product_id)->first()->name;
        }],
            'comment' => 'comment',
            'rating' => 'rating',
            'approved' => 'approved',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'confirm' => ['product_name', function($model) {

                return '<form action="'.route('admin.review.approve' , $model->id).'" method="post">
                            <input type="hidden" name="_token" value="'. csrf_token() .'">
                            <input type="hidden" name="approved" value="1">
                            <button type="submit">Approve</button>
                         </form>';

            }],
        ]);


        $table->editable(true, url('admin/users/reviews/{id}/edit'));
        $table->destroyable(true, url('admin/users/reviews/{id}'));


        $table->sortable(true, [
            'user_id','product_id', 'created_at','updated_at', 'rating', 'approved'
        ]);


        return view('admin.user_reviews')
            ->with([
                'review' => $review,
                'table' => $table->render(),
                'pagination' => $table->paginator->render(),
        ]);

    }

}
