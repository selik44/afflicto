<?php namespace Friluft\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Friluft\Product;

class GenerateProductSummary extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'product:summarize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Automatically generate summaries on products, based on description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	private function fromHTMLParagraph($s) {
		$p = str_replace("\n", "", $s);
		if (preg_match('/(*ANY)<[pP]>(.+)<\/[pP]>/', $p, $matches)) {
			$summary = strip_tags($matches[1]);
			return $summary;
		}
	}

	private function fromDoubleNewlines($s) {
		$s = strip_tags($s);
		$paragraphs = explode("\n\n", $s);
		if (count($paragraphs) > 0) {
			$summary = $paragraphs[0];

			foreach($paragraphs as $p) {
				if (mb_strlen($p) > mb_strlen($summary)) {
					$summary = $p;
				}
			}

			return $summary;
		}

		return "";
	}

	public function fromSentences($s) {
		$s = strip_tags($s);

		# get everything untill first period?
		if (preg_match('/((([^\.])|(.[^\ ])))+/', $s, $matches)) {
			return $matches[1];
		}
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		foreach(Product::all() as $product) {
			$this->info('------GENERATING SUMMARY-------');
			$string = html_entity_decode($product->description);

			# trim
			$string = trim($string);

			# skip empty ones
			if (mb_strlen($string) <= 0) continue;

			# shorten 
			$string = substr($string, 0, 350);

			$methods = ['fromHTMLParagraph', 'fromDoubleNewlines', 'fromSentences'];

			$results = [];

			foreach($methods as $method) {
				# run this method
				$result = $this->{$method}($string);
				$results[] = $result;

				# combine it with the others
				foreach($methods as $method) {
					$result = $this->{$method}($result);
					$results[] = $result;
				}
			}

			$this->info('Results!');
			foreach($results as $key => $result) {
				$this->comment('Result ' .$key);
				$this->comment($result);
			}

			$this->info('Final Result for ' .$product->name .':');

			# get the best one
			$final = $results[0];
			$finalLength = mb_strlen($final);
			foreach($results as $result) {
				$length = mb_strlen($result);
				if ($length > $finalLength) {
					$final = $result;
					$length = $finalLength;
				}
			}

			$this->comment($final);

			# save the model
			$product->summary = $final;
			$product->save();
		}
		
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
			//['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
