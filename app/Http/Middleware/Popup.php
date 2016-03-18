<?php

namespace Friluft\Http\Middleware;

use Closure;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use Cookie;
use Illuminate\Support\Facades\Auth;
use Session;

class Popup
{

	const CookieName = "popup_newsletter_seen";
	const SessionName = "popup_newsletter";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
	    # have we seen the popup before?
	    if ($request->cookie(self::CookieName, 'false') == 'false' || true) {
		    /**
		     * @var CookieJar $cookieJar
		     */
		    $cookieJar = app(CookieJar::class);

		    # trigger the popup
		    Session::flash(self::SessionName, 'true');

		    if (Auth::user()) {
			    $user = Auth::user();
			    \Former::populate($user);
		    }

		    # make sure we don't see the popup twice
		    $cookieJar->queue(Cookie::forever(self::CookieName, 'true'));
	    }

        return $next($request);
    }
}
