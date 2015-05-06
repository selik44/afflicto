<?php namespace Friluft\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'Friluft\Http\Middleware\VerifyCsrfToken',
		'Friluft\Http\Middleware\StoreDetector',
		'Friluft\Http\Middleware\JavascriptMiddleware',
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => 'Friluft\Http\Middleware\Authenticate',
		'admin' => 'Friluft\Http\Middleware\AuthenticateAdmin',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		'guest' => 'Friluft\Http\Middleware\RedirectIfAuthenticated',
	];

}
