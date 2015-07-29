<?php namespace Friluft\Http\Middleware;

use Closure;

class LocaleDetector {

	public static $tlds = [
		'com' => 'en',
		'no' => 'no',
		'se' => 'se',
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
		# get TLD
		$host = array_reverse(
			explode('.',
				strtolower(
					$request->getHost()
				)
			)
		);
		$host = $host[0];

		# set locale
		$lang = 'en';
		if (isset(static::$tlds[$host])) {
			$lang = static::$tlds[$host];
		}

		\App::setLocale($lang);

		return $next($request);
	}

}
