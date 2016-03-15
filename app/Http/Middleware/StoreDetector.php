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

		# get store
		$store = Store::where('host', '=', $host)->first();
		if ( ! $store) {
			$store = Store::where('machine', '=', 'friluft')->first();
		}

		# set store
		Store::setCurrentStore($store);

		# continue
		return $next($request);
	}

}
