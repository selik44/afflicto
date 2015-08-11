<?php namespace Friluft\Console\Commands;

use Illuminate\Console\Command;
use Friluft\Product;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

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

	public function fire() {
		$iterator = new \DirectoryIterator(public_path('images/products'));
		foreach($iterator as $file) {
			if ($file->isDot() || $file->isDir()) continue;

			# is this already a thumbnail? if so, delete it.
			if (preg_match('/_thumbnail\.[a-zA-Z0-9]+$/', $file->getFilename()) === 1) {
				unlink($file->getRealPath());
				continue;
			}
			$this->comment('reading ' .$file->getFilename());
			try {
				$img = \Img::make($file);
				$img->resize(800, null, function ($constraint) {
					$constraint->upsize();
					$constraint->aspectRatio();
				})->save();

				$info = $file->getFileInfo();
				$thumbnail = $info->getPath() .'/' .$info->getBasename('.' .$info->getExtension()) .'_thumbnail.' .$info->getExtension();

				# generate thumbnail
				$img->resize(null, 200, function ($constraint) {
					$constraint->upsize();
					$constraint->aspectRatio();
				})->save($thumbnail);
			}catch(\Exception $e) {
				$this->comment('Error: ' .$e->getMessage());
			}
		}
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire_old()
	{
		foreach(Product::all() as $product) {
			foreach($product->images as $image) {
				$pathinfo = pathinfo($image->name);
				$filename = $pathinfo['filename'];
				$extension = $pathinfo['extension'];

				$image->name = $filename .'.' .$extension;

				try {
					$img = \Img::make(public_path('images/products') . '/' . $image->name);
					$img->resize(800, null, function ($constraint) {
						$constraint->upsize();
						$constraint->aspectRatio();
					})->save();


					# generate thumbnail
					$img->resize(null, 200, function ($constraint) {
						$constraint->upsize();
						$constraint->aspectRatio();
					})->save(public_path('images/products') . '/' . $filename . '_thumbnail.' . $extension);
				}catch(\Exception $e) {
					$this->comment("Error: " .$e->getMessage());
				}
			}
		}
	}

}
