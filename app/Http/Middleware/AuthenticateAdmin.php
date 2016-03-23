<?php namespace Friluft\Http\Middleware;

use Closure;
use Config;
use Illuminate\Http\Request;

class AuthenticateAdmin extends Authenticate {

	protected function forbidden(Request $request)
	{
		if ($request->wantsJson()) {
			return response("Forbidden", 403);
		}else {
			return \Redirect::home()->with('error', "You don't have permission to do that.");
		}
	}

	protected function authorize(Request $request) {
		return false;
	}

	public function handle($request, Closure $next)
	{
		# authenticated?
		if ($this->authenticate() !== true) {
			return $this->unauthorized($request);
		}

		# do we have access?
		if ($this->authorize($request) !== true) {
			return $this->forbidden($request);
		}

		return $next($request);
	}

}
