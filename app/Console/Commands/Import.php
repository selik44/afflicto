<?php namespace Friluft\Console\Commands;

use Friluft\Image;
use Friluft\Manufacturer;
use Friluft\Vatgroup;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Friluft\Utils\Mystore;
use Friluft\Category;
use Friluft\Product;
use DB;
use Illuminate\Support\Str;

class Import extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mystore:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Imports all the products and categories from MyStore.';

	private $attributes = [
		'name',
		'url_identifier:slug',
		'quantity:stock',
		'weight',
		'status:enabled',
		'price_ex_tax:price',
		'description',
	];

	private $idMap = [];

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	private function importCategories() {
		# create categories
		$cats = Mystore::categories();
		foreach($cats as $cat) {
			$c = new Category();
			$c->name = $cat['name'];
			$c->slug = Str::slug($c->name);
			$c->save();

			$this->idMap[$cat['id']] = $c->id;
			$this->comment('Mapping mystore ID ' .$cat['id'] .' to ' .$c->id);
		}

		$this->comment('Creating relationships...');
		foreach($cats as $cat) {
			# is this a child?
			if ($cat['type'] == 'child') {
				# get the category model
				$model = Category::find($this->idMap[$cat['id']]);
				$model->parent()->associate(Category::find($this->idMap[$cat['parent']]));
				$model->save();
			}
		}

		$this->comment('done! ' .count($cats) .' categories imported.');
	}

	private function importProducts() {
		$page = 1;
		$products = Mystore::products($page);
		while(count($products) > 0) {
			foreach($products as $product) {
				$p = new Product();
				foreach($this->attributes as $attribute) {
					$exploded = explode(':', $attribute);
					if (isset($exploded[1])) {
						$p->{$exploded[1]} = $product['products_' .$exploded[0]];
					}else {
						$p->{$exploded[0]} = $product['products_' .$exploded[0]];
					}
				}

				$catids = [];
				foreach($product['products_categories'] as $id) {
					if (isset($this->idMap[$id])) {
						$catids[] = $this->idMap[$id];
					}
				}

				$p->vatgroup()->associate(Vatgroup::where('name', '=', '25%')->first());

				$p->manufacturer()
					->associate(Manufacturer::where('slug', '=', 'highpulse')->first());

				$p->save();

				# download the images from mystore
				$i = 0;
				if (isset($product['products_images']) && is_array($product['products_images'])) {
					foreach($product['products_images'] as $image) {	
						# get pathinfo
						$pathinfo = pathinfo($image);

						# the fileName for our image
						$fileName = 'product_' .$p->id .'_' .$i .'.' .$pathinfo['extension'];

						# dwonload the image
						if (!file_exists($filePath = public_path() .'/images/products/' .$fileName)) {
							# we need to urlencode the filename, since this is an http request.
							$url = $pathinfo['dirname'] .'/' .rawurlencode($pathinfo['basename']);
							$this->comment("Downloading image: $url...");

							# attempt download
							if (@copy($url, $filePath)) {
								# success
								$image = new Image();
								$image->name = 'product_' .$p->id .'_' .$i .'.' .$pathinfo['extension'];
								$image->type = 'product';
								$image->save();

								$p->images()->save($image);
							}else {
								# fail
								$this->comment("Failed to download image '" .$url ."' for product id " .$p->id ."!");
							}

						}

						$i++;
					}
				}

				# save it again
				$p->save();

				# sync categories
				$p->categories()->sync($catids);

			}
			$page++;
			$products = Mystore::products($page);
		}
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		if ($this->confirm('delete all categories and products?')) {
			$this->comment('Truncating "categories" and "products" table, as well as deleting all images in public/images/products...');
		    DB::table('categories')->delete();
		    DB::table('products')->delete();
		    DB::table('category_product')->delete();
		    foreach(glob(public_path() .'/images/products/*') as $img) {
		    	@unlink($img);
		    }
	    	$this->comment('Ok.');
	    }

        $this->comment('Importing categories...');
        $this->importCategories();
        $this->comment('Ok.');

        $this->comment('Importing products...');
        $this->importProducts();
        $this->comment('Done!');
	}

}
