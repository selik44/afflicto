<?php namespace Friluft\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Routes that match these patterns will be run regardless of the token.
	 * @var [type]
	 */
	private $skippedRoutes = ['store/push'];

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		# are we accessing a route that doesn't want CSRF protection?
		foreach($this->skippedRoutes as $route) {
			if ($request->is($route)) {
				return $next($request);
			}
		}

		return parent::handle($request, $next);
	}

}
