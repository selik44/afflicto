<?php

namespace Friluft\Http\Middleware;

use Closure;

class Permissions extends Authenticate
{
	protected function forbidden($request) {
		if ($request->ajax() || $request->wantsJson())
		{
			return response('Forbidden.', 403);
		}
		else
		{
			return redirect()->guest(route('home'))->setStatusCode(403)->with('warning', "You are not authorized to do that.");
		}
	}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions = [])
    {
		if ($this->authenticate() !== true) {
			return $this->unauthorized($request);
		}

		$user = $this->auth->user();
		if ($user->role->has($permissions) !== true) {
			return $this->forbidden($request);
		}

        return $next($request);
    }
}
