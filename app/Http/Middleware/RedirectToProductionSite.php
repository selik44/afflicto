<?php

namespace Friluft\Http\Middleware;

use Closure;

class RedirectToProductionSite
{

    private $allowedIPAddresses = [
		#'84.212.14.10',
		'127.0.0.1',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->getClientIp();

		if ( ! in_array($ip, $this->allowedIPAddresses)) {
			return \Redirect::away('http://www.123friluft.no');
		}

		return $next($request);
    }
}
