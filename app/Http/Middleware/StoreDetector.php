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
		$store = Store::where('url', '=', $serverName)->first();
		if ($store) {
			Store::setCurrentStore($store);
			return $next($request);
		}else {
			throw new Exception("Store not found for server_name: " .$serverName ."!", 1);
		}
	}

}
