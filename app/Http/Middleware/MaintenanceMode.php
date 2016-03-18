<?php

namespace Friluft\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Application;

class MaintenanceMode
{
	/**
	 * The application implementation.
	 *
	 * @var \Illuminate\Contracts\Foundation\Application
	 */
	protected $app;

	/**
	 * Create a new middleware instance.
	 *
	 * @param Application|\Illuminate\Contracts\Foundation\Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 * @throws HttpException
	 */
	public function handle($request, Closure $next)
	{
		if ($this->app->isDownForMaintenance()) {

			# is logged in?
			if (\Auth::check()) {
				# is admin?
				if (\Auth::user()->role->has('admin.access')) {
					# allow access anyway
					return $next($request);
				}
			}

			throw new HttpException(503);
		}

		return $next($request);
	}
}
