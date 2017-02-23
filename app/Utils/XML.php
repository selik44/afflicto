<?php

namespace Friluft\Utils;

class XMLNode {
	public $name = 'node';

	public $value = null;

	public $children = [];

	public function __construct($name, $value = null) {
		$this->name = $name;
		if (is_array($value) || $value instanceof XMLNode) {
			$this->add($value);
			$this->value = null;
		}else {
			$this->value = $value;
		}
		return $this;
	}

	public function name($name = null) {
		$this->name = $name;
		return $this;
	}

	public function value($value = null) {
		$this->value = $value;
		return $this;
	}

	/**
	 * @param $stuff
	 * @param null $value
	 * @return $this|XMLNode returns $this, or the newly created XMLNode, if it creates one from a key and value.
	 */
	public function add($stuff, $value = null) {
		if ($value !== null || is_string($stuff)) {
			$node = new XMLNode($stuff, $value);
			$this->children[] = $node;
			return $node;
		}else if ($stuff instanceof XMLNode) {
			$this->children[] = $stuff;
		}else if (is_array($stuff)) {
			foreach($stuff as $key => $value) {
				$this->add($key, $value);
			}
		}

		return $this;
	}

	public function render($pretty = false) {
		# pretty print?
		if ($pretty) {
			$str = '<' .$this->name .'>' .PHP_EOL;
		}else {
			$str = '<' .$this->name .'>';
		}

		if ($this->value instanceof XMLNode) {
			$str .= $this->value->render();
		}else if ($this->value != null) {
			$str .= $this->value;
		}else {
			foreach($this->children as $child) {
				$str .= $child->render($pretty);
			}
		}
		$str .= '</' .$this->name .'>';
		return $str;
	}

}

class XML extends XMLNode {

	const ISO_8859_1 = "ISO-8859-1";
	const UTF_8 = "UTF-8";

	private $encoding = 'UTF-8';

	public function __construct($name, $encoding = self::UTF_8) {
		$this->name = $name;
		$this->value = null;
		$this->encoding = $encoding;
	}

	public function render($pretty = false) {
		$str = '<?xml version="1.0" encoding="' .$this->encoding .'"?>';

		# open root node
		if ($pretty) {
			$str .= '<' .$this->name .'>' .PHP_EOL;
		}else {
			$str .= '<' .$this->name .'>';
		}

		# render children
		foreach($this->children as $child) {
			$str .= $child->render($pretty);
		}

		# close root node
		$str .= '</' .$this->name .'>';

		if ($this->encoding == self::ISO_8859_1) {
			return utf8_decode($str);
		}

		return $str;
	}
}