<?php namespace Friluft\Http\Middleware;

use Closure;
use Javascript;
use Cart;

class JavascriptMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		JavaScript::put([
			'URL' => url() .'/' .\App::getLocale(),
			'token' => csrf_token(),
			'request' => ['path' => $request->path()],
			//'cart' => ['contents' => Cart::getItemsWithModels(true), 'total' => Cart::getTotal()],
		]);

		return $next($request);
	}

}
