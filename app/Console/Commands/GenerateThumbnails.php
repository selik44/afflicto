<?php namespace Friluft\Console\Commands;

use Illuminate\Console\Command;
use Friluft\Product;

class GenerateThumbnails extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'product:thumbnails';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Imports all the products and categories from MyStore.';

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		foreach(Product::all() as $product) {
			foreach($product->images as $image) {
				$pathinfo = pathinfo($image->name);
				$filename = $pathinfo['filename'];
				$extension = $pathinfo['extension'];

				$image->name = $filename .'.' .$extension;

				$img = \Img::make(public_path('images/products') .'/' .$image->name);
				$img->fit(800, null, function($constraint) {
					$constraint->upsize();
				})->save();

				# generate thumbnail
				$img->fit(200, 200)->save(public_path('images/products') .'/' .$filename .'_thumbnail.' .$extension);
			}
		}
	}

}
