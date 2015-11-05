<?php namespace Friluft\PDF;

use Knp\Snappy\Pdf;

/**
 * Wrapper for Snappy PDF
 * @package Friluft\PDF
 */
class Snappy {

	public $binary = null;
	public $options = [];
	public $env = [];

	public function __construct($binary, $options = [], $env = []) {
		$this->binary = $binary;
		$this->options = $options;
		$this->env = $env;
	}

	/**
	 *
	 * @return \Knp\Snappy\Pdf
	 */
	public function make() {
		return new Pdf($this->binary, $this->options, $this->env);
	}

	/**
	 * @param string $html
	 * @param string $fileName
	 * @param boolean $overWrite
	 * @return \Knp\Snappy\Pdf
	 */
	public function fromHTML($html, $fileName, $overWrite) {
		$pdf = $this->make();
		$pdf->generateFromHtml($html, $fileName, $overWrite);

		return $pdf;
	}

}