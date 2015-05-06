<?php namespace Friluft\Http\Controllers\Admin;

use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DashboardController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->view('admin.dashboard');
	}

}
