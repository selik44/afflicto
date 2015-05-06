<?php namespace Friluft\Http\Middleware;

use Closure;
use Javascript;

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
			'URL' => url(),
			'token' => csrf_token(),
			'request' => ['path' => $request->path()]
		]);

		return $next($request);
	}

}
