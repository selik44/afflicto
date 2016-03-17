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
			$c->name = ucwords(strtolower($cat['name']));
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

				# price
				$p->price = $product['products_price_ex_tax'];

				$catids = [];
				foreach($product['products_categories'] as $id) {
					if (isset($this->idMap[$id])) {
						$catids[] = $this->idMap[$id];
					}
				}

				$p->vatgroup()->associate(Vatgroup::where('name', '=', '25%')->first());

				$p->manufacturer_id = null;

				$mf = $product['products_brand_name'];
				if (mb_strlen($mf) > 0) {
					$manufacturer = Manufacturer::where('name', '=', $mf)->first();
					if ( ! $manufacturer) {
						$manufacturer = new Manufacturer();
						$manufacturer->name = $mf;
						$manufacturer->slug = Str::slug($mf, '-');
						$manufacturer->save();
					}

					$p->manufacturer()->associate($manufacturer);
				}

				# remove manufacturer name from product name
				if ($p->manufacturer) {
					if ($p->name == 'HighPulse') {
						$p->name = preg_replace('/^(h|H)igh *(p|P)ulse/', '', $p->name);
					}else {
						$p->name = str_replace($p->manufacturer->name, '', $p->name);
					}
				}

				$p->save();

				# download the images from mystore
				if (isset($product['products_images']) && is_array($product['products_images'])) {
					foreach($product['products_images'] as $image) {
						# get pathinfo
						$pathinfo = pathinfo($image);

						$hasImage = false;
						# download the image

						$filePath = public_path('images/products') .'/' .$pathinfo['basename'];

						if (!file_exists($filePath)) {
							# we need to urlencode the filename, since this is an http request.
							$url = $pathinfo['dirname'] .'/' .rawurlencode($pathinfo['basename']);
							$this->comment("Downloading image: $url...");

							# attempt download
							$hasImage = false;
							if (@copy($url, $filePath)) {
								$hasImage = true;
							}else {
								$this->comment("Failed to download image '" .$url ."' for product id " .$p->id ."!");
							}
						}else {
							$hasImage = true;
							$this->comment('Image already exists, skipping download.');
						}

						if ($hasImage) {
							$image = new Image();
							$image->name = $pathinfo['basename'];
							$image->type = 'product';
							$p->images()->save($image);
						}
					}
				}

				# sync categories
				$p->categories = $catids;
				$p->save();
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
			$this->comment('Truncating "categories", "manufacturers" and "products" tables.');
		    DB::table('categories')->delete();
		    DB::table('products')->delete();
		    DB::table('category_product')->delete();
			DB::table('manufacturers')->delete();
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
