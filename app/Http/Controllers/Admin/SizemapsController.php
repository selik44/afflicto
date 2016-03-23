<?php

namespace Friluft\Http\Controllers\Admin;

use Friluft\Sizemap;
use Illuminate\Http\Request;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Input;

class SizemapsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $table = \Laratable::make(Sizemap::query(), [
			'#' => 'id',
			'Navn' => 'name',
			'Brukes' => ['_usage', function($model) {
				$c = $model->products()->count();
				if ($c == 0) {
					return 'Ingen produkter';
				}else if ($c == 1) {
					return '1 produkt.';
				}else {
					return $c .' produkter';
				}
			}],
		]);

		$table->editable(true, url('admin/sizemaps/{id}/edit'));
		$table->destroyable(true, url('admin/sizemaps/{id}'));
		$table->sortable(true, [
			'id', 'name',
		]);

		return view('admin.sizemaps_index')->with([
			'table' => $table->render(),
			'pagination' => $table->paginator->render(),
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.sizemaps_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\Admin\CreateSizemapRequest $request)
    {
        $sizemap = new Sizemap();
		$sizemap->name = $request->get('name');

		$image = Input::file('image');

		$fileName = $image->getClientOriginalName();
		$image->move(public_path('images/sizemaps'), $fileName);
		$sizemap->image = $fileName;

		$sizemap->save();

		return \Redirect::route('admin.sizemaps.index')->with('success', 'Sizemap created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Sizemap $sizemap)
    {
		\Former::populate($sizemap);
		return view('admin.sizemaps_edit')->with('sizemap', $sizemap);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\Admin\UpdateSizemapRequest $request, Sizemap $sizemap)
    {
		$sizemap->name = $request->get('name');

		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$fileName = $file->getClientOriginalName();
			$file->move(public_path('images/sizemaps'), $fileName);
			$sizemap->image = $fileName;
		}

		$sizemap->save();

		return \Redirect::route('admin.sizemaps.index')->with('success', 'Sizemap updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sizemap $sizemap)
    {
        $sizemap->delete();
		return \Redirect::back()->with('success', 'Størrelse-kart slettet.');
    }
}
