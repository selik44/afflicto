<?php namespace Friluft\Http\Middleware;

use Closure;
use Friluft\Store;
use Exception;
use Illuminate\Http\Request;

class StoreDetector {

	public function handle(Request $request, Closure $next)
	{
		$host = array_reverse(
			explode('.',
				strtolower(
					$request->getHost()
				)
			)
		);

		$host = $host[1];

		$store = Store::where('host', '=', $host)->first();
		if ($store) {
			Store::setCurrentStore($store);
			return $next($request);
		}else {
			throw new Exception("Store not found for host: " .$host ."!", 1);
		}
	}

}
