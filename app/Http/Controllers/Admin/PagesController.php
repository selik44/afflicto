<?php

namespace Friluft\Http\Controllers\Admin;

use Friluft\Page;
use Illuminate\Http\Request;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Input;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $table = \Laratable::make(Page::query(), [
			'#' => 'id',
			'Title' => 'title',
			'Slug' => 'slug',
		]);

		$table->sortable(true, ['id', 'title', 'slug']);
		$table->paginate(true, 20);

		$table->editable(true, url('admin/pages/{id}/edit'));
		$table->destroyable(true, url('admin/pages/{id}'));

		return view('admin.pages_index')->with([
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
        return view('admin.pages_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store()
    {
        $page = new Page(Input::only(['title', 'slug', 'content']));

		$page->options = [
			'sidebar' => Input::has('sidebar'),
		];

		$page->save();

		return \Redirect::route('admin.pages.index')->with('success', 'Page created!');
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Page $page
	 * @return Response
	 */
    public function edit(Page $page)
    {
		\Former::populate(array_merge($page->toArray(), ['sidebar' => $page->options['sidebar']]));

        return view('admin.pages_edit')->with([
			'page' => $page,
		]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Page $page)
    {
		$options = $page->options;
		$options['sidebar'] = Input::has('sidebar');
		$page->options = $options;
		$page->update(Input::only(['title', 'slug', 'content']));

		return \Redirect::route('admin.pages.index')->with('success', 'Page updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Page $page)
    {
        $page->delete();

		return \Redirect::route('admin.pages.index')->with('success', 'Page #' .$page->id .' deleted!');
    }
}
