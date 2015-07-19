<?php namespace Friluft\Http\Middleware;

use Closure;

class LocaleDetector {

	public static $supportedLocales = [
		'en' => 'English',
		'no' => 'Norsk',
		'se' => 'Swedish',
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
		$lang = $request->segment(1);

		if ( ! isset(static::$supportedLocales[$lang])) {
			# detect locale from HTTP request
			$lang = strtolower(substr($request->server->get('HTTP_ACCEPT_LANGUAGE'), 0, 2));

			if ( ! isset(static::$supportedLocales[$lang])) {
				$lang = 'en';
			}
		}

		\App::setLocale($lang);

		return $next($request);
	}

}
