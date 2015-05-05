<?php namespace Friluft\Http\Middleware;

use Closure;
use App;

class Language {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		$lang = $request->segment(0);

		dd($request);

		if ($lang) {
			if (preg_match('/(en)|(no)|(se)/', $lang)) {
				App::setLocale($lang);
			}
		}

		dd($lang);

		return $next($request);
	}

}
