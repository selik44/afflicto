<?php namespace Friluft\Console\Commands;

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
		'model',
		'url_identifier:slug',
		'quantity:stock',
		'weight',
		'status:enabled',
		'in_price',
		'price_ex_tax:price',
		'tax_percentage',
		'description',
	];

	private $idMap = [];

	private $imagesStartID = 1072;

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	private function importCategories() {
		//create categories
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
			//is this a child?
			if ($cat['type'] == 'child') {
				//get the category model
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
		$imageID = 1072;
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

				$p->save();

				# get the images for this product
				$images = [];		# array of image paths (relative to public/images/products)
				foreach(glob(public_path() .'/images/products/product_' .$imageID .'_image_*') as $img) {
					$info = pathinfo($img);
					$images[] = $info['basename'];
				}

				# save the images array on the model as JSON.
				$p->images = $images;

				# save it again
				$p->save();

				//sync categories
				$p->categories()->sync($catids);

				//increment the imagesID for the next product
				$imageID++;
				
				# download the images from mystore
				/*
				$i = 0;
				$images = [];
				if (isset($product['products_images']) && is_array($product['products_images'])) {
					foreach($product['products_images'] as $image) {	
						# get pathinfo
						$pathinfo = pathinfo($image);

						# the fileName for our image
						$fileName = 'product_' .$p->id .'_image_' .$i .'.' .$pathinfo['extension'];

						# dwonload the image
						if (!file_exists($filePath = public_path() .'/images/products/' .$fileName)) {
							# we need to urlencode the filename, since this is an http request.
							$url = $pathinfo['dirname'] .'/' .rawurlencode($pathinfo['basename']);
							$this->comment("Downloading image: $url...");

							# attempt download
							if (@copy($url, $filePath)) {
								# success
								$images[] = $fileName;
							}else {
								# fail
								$this->comment("Failed to download image '" .$url ."' for product id " .$p->id ."!");
							}

						}

						$i++;
					}
				}*/

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
		$opts = $this->getOptions();
		if (in_array('delete', $opts[0])) {
			$this->comment('Truncating "categories" and "products" table...');
		    DB::table('categories')->delete();
		    DB::table('products')->delete();
		    DB::table('category_product')->delete();

		    $this->comment('Deleting product images...');
		    //array_map('unlink', glob(public_path() .'/images/products/*'));

		    $this->comment('Ok.');
		}

        $this->comment('Importing categories...');
        $this->importCategories();

        $this->comment('Importing products...');
        $this->importProducts();
        $this->comment('Done!');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			//['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			//['download', null, InputOption::VALUE_OPTIONAL, 'download images from mystore', null],
		];
	}

}
