<?php

namespace Friluft\Console\Commands;


use Friluft\Utils\Mystore;
use Illuminate\Console\Command;

class ImportImages extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mystore:importImages';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Imports images from mystore.';

	public function fire() {
		$page = 1;
		$products = Mystore::products($page);
		while(count($products) > 0) {
			foreach($products as $product) {
				# download the images from mystore
				if (isset($product['products_images']) && is_array($product['products_images'])) {
					foreach($product['products_images'] as $image) {
						# get pathinfo
						$pathinfo = pathinfo($image);

						$filePath = public_path('images/products') .'/' .$pathinfo['basename'];

						if (!file_exists($filePath)) {
							# we need to urlencode the filename, since this is an http request.
							$url = $pathinfo['dirname'] .'/' .rawurlencode($pathinfo['basename']);
							$this->comment("Downloading image: $url...");
							@copy($url, $filePath);
						}
					}
				}

			}
			$page++;
			$products = Mystore::products($page);
		}
	}

}