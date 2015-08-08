<?php

namespace Friluft\Http\Controllers\Admin;

use Friluft\Tile;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

class TilesController extends Controller
{
    public function index()
    {
        return view('admin.tiles_index')->with([
			'tiles' => Tile::all(),
		]);
    }

	public function update() {

	}

	public function addTile() {
		
	}

}
