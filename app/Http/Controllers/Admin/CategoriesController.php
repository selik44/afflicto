<?php namespace Friluft\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Friluft\Utils\Datatable;
use Friluft\Category;

class CategoriesController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($page = 1, $column = 'id', $direction = 'asc')
	{
		$table = Datatable::make('categories', 'Friluft\Category', [
			'id' => '#',
			'name' => 'Name',
			'slug' => 'Slug',
			'parent' => 'Parent',
		]);

		$table->option('url', url() .'/admin/categories/{page}/{column}/{direction}');
		$table->paginate(15, $page);

		$table->sort(['id', 'name', 'slug', 'parent'], $column, $direction);

		$table->destroyable('admin/categories/destroy/{id}');
		$table->editable('admin/categories/edit/{id}');

		return $this->view('admin.categories_index')->with('table', $table->display());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return $this->view('admin.categories_create')->with('categories', Category::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$category = new Category(Input::all());
		$category->save();

		if (Input::has('continue')) {
			return redirect('admin/categories/create');
		}

		return redirect('admin/categories');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Category $category)
	{
		
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Category $category)
	{
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Category $category)
	{
		
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		DB::table('categories')->where('id', '=', $id)->delete();

		return Redirect::to('admin/categories')->with('success', 'Category deleted.');
	}

}
