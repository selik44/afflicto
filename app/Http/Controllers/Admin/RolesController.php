<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Category;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Permission;
use Friluft\Role;
use Friluft\User;
use Auth;
use Laratable;
use Input;
use Redirect;
use Former;

class RolesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$table = Laratable::make(Role::query(), [
			'#' => 'id',
			'Name' => 'name',
			'Machine' => 'machine',
			'Users' => ['users', function($model) {
				return $model->users()->count();
			}],
		]);

		$table->editable(true, url('admin/roles/{id}/edit'));
		$table->destroyable(true, url('admin/roles/{id}'));

		return $this->view('admin.roles_index')
			->with([
				'table' => $table->render(),
				'pagination' => $table->paginator->render(),
			]);
	}

	public function create()
	{
		return $this->view('admin.roles_create')
			->with([
				'form' => form('admin.role'),
				'permissions' => Permission::all(),
			]);
	}

	public function store(Requests\CreateRoleRequest $request)
	{
		$role = new Role(Input::all());
		$role->save();
		$role->permissions()->sync(Input::get('permissions', []));

		return Redirect::route('admin.roles.index')->with('success', trans('admin.role_create_success', ['role' => $role->name]));
	}

	public function edit(Role $role)
	{
		if ( ! $role->editable) return Redirect::back()->with('warning', $role->machine .' kan ikke endres.');

		Former::populate($role);

		return $this->view('admin.roles_edit')
			->with([
				'role' => $role,
				'form' => form('admin.role'),
				'permissions' => Permission::all(),
			]);
	}

	public function update(Requests\UpdateRoleRequest $request, Role $role)
	{
		if ( ! $role->editable) return Redirect::back()->with('warning', $role->machine .' kan ikke endres.');

		if ($role->machine === 'admin') {
			return Redirect::back()->with('warning', trans('validation.disallow_admin_role_edit', ['role' => $role->name]));
		}

		$role->name = Input::get('name', null) or $role->name;
		$role->permissions()->sync(Input::get('permissions', []));
		$role->save();

		return Redirect::route('admin.roles.index')->with('success', trans('admin.role_update_success', ['role' => $role->name]));
	}

	public function destroy(Role $role)
	{
		if ( ! $role->editable) {
			return Redirect::back()->with('warning', 'Den rollen kan ikke endres.');
		}

		$role->delete();

		return Redirect::route('admin.roles.index')->with('Role ' .e($role->name) .' deleted.');
	}

}
