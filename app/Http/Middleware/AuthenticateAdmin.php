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
		# not logged in?
		if (\Auth::check() == false) return false;

		# is the user an 'admin'? if so we'll give full access to anything.
		if (\Auth::user()->role->machine === 'admin') return true;

		# get the permissions required for the current route
		$perms = Config::get('access.' .str_replace('.', '_', $request->route()->getName()));
		
		# better safe than sorry... nope!
		if (!$perms) return false;

		# does the user have these
		return \Auth::user()->role->has($perms);
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
