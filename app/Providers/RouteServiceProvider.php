<?php namespace Friluft\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Friluft\Category;
use Friluft\Product;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'Friluft\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		//
		
		parent::boot($router);
		$router->pattern('id', '[0-9]+');
		$router->pattern('page', '[0-9]+');
		$router->pattern('column', '[a-z-A-Z0-9-_]+');
		$router->pattern('direction', '(asc)|(desc)|(ASC)|(DESC)');

		$router->model('user', 'Friluft\User');
			
		# Bind category and product to find models by ID or slug.
		$router->bind('category', function($value) {
			$cat = Category::where('id', '=', $value)->orWhere('slug', '=', $value)->first();
			if ($cat) return $cat;
			abort(404);
		});

		$router->bind('product', function($value) {
			$product = Product::where('id', '=', $value)->orWhere('slug', '=', $value)->first();
			if ($product) return $product;
			abort(404);
		});
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group(['namespace' => $this->namespace], function($router)
		{
			require app_path('Http/routes.php');
		});
	}

}
