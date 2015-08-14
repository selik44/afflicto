<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Controllers\Controller;
use Friluft\Http\Requests\CreateUserRequest;
use Friluft\Role;
use Friluft\User;
use Illuminate\Support\Facades\Auth;
use Laratable;
use Former;
use Input;
use Redirect;

class UsersController extends Controller {

	public function index()
	{
		$table = Laratable::make(User::query()->where('role-id', '!=', Role::where('machine', '=', 'admin')->first()->id), [
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
		$table = Laratable::make(User::where('role_id', '=', Role::whereMachine('regular')->first()->id), [
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
				if (Auth::user()->role->machine !== 'admin') {
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

}
