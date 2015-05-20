<?php namespace Friluft\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class Authenticate {

	protected $auth;

	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	protected function unauthorized(Request $request) {
		if ($request->ajax() || $request->wantsJson())
		{
			return response('Unauthorized.', 401);
		}
		else
		{
			return redirect()->guest(route('user.login'))->with('warning', "Authentication failed.");
		}
	}

	/**
	 * Check if the user is logged in.
	 * @return bool true if the user is logged in, false otherwise.
	 */
	public function authenticate() {
		return $this->auth->check();
	}

	public function handle($request, Closure $next)
	{
		if ($this->authenticate() !== true) {
			return $this->unauthorized($request);
		}

		return $next($request);
	}

}
