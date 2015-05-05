<?php namespace Friluft\Http\Middleware;

use Closure;
use Friluft\Store;

class StoreDetector {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$serverName = strtolower($_SERVER['SERVER_NAME']);
		Store::setCurrentStore($serverName);
		return $next($request);
	}

}
