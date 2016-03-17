<?php namespace Friluft\Http\Middleware;

use Closure;

class AuthenticateBasic {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (\Auth::basic('email') == false) {
			return response('Authentication Error.', 401);
		}

		return $next($request);
	}

}
