<?php namespace Friluft;

use Former\Traits\Field;
use Former\Traits\Framework;
use HtmlObject\Element;

class GentleStyleFormFramework extends Framework implements \Former\Interfaces\FrameworkInterface {

	public $app;

	public function __construct($app) {
		$this->app = $app;
	}

	/**
	 * Filter buttons classes
	 *
	 * @param  array $classes An array of classes
	 *
	 * @return array A filtered array
	 */
	public function filterButtonClasses($classes) {
		return $classes;
	}

	/**
	 * Filter field classes
	 *
	 * @param  array $classes An array of classes
	 *
	 * @return array A filtered array
	 */
	public function filterFieldClasses($classes) {
		return $classes;
	}

	/**
	 * Add classes to a field
	 *
	 * @param Field $field
	 * @param array $classes The possible classes to add
	 *
	 * @return Field
	 */
	public function getFieldClasses(Field $field, $classes) {
		return $field;
	}

	/**
	 * Add group classes
	 *
	 * @return string A list of group classes
	 */
	public function getGroupClasses() {
		return 'form-group';
	}

	/**
	 * Add label classes
	 *
	 * @return array An array of attributes with the label class
	 */
	public function getLabelClasses() {
		return [];
	}

	/**
	 * Add uneditable field classes
	 *
	 * @return array An array of attributes with the uneditable class
	 */
	public function getUneditableClasses() {
		return ['disabled' => 'disabled'];
	}

	/**
	 * Add plain text field classes
	 *
	 * @return array An array of attributes with the plain text class
	 */
	public function getPlainTextClasses() {
		return ['class' => 'gs-plaintext'];
	}

	/**
	 * Add form class
	 *
	 * @param  string $type The type of form to add
	 *
	 * @return array
	 */
	public function getFormClasses($type) {
		return [];
	}

	/**
	 * Add actions block class
	 *
	 * @return array
	 */
	public function getActionClasses() {
		return ['button-group'];
	}

	/**
	 * Render an help text
	 *
	 * @param string $text
	 * @param array  $attributes
	 *
	 * @return string
	 */
	public function createHelp($text, $attributes = array()) {
		return "<span class='muted'>$text</span>";
	}

	/**
	 * Render a disabled field
	 *
	 * @param Field $field
	 *
	 * @return string
	 */
	public function createDisabledField(Field $field) {
		return Element::create('span', $field->getValue(), $field->getAttributes());
	}

	/**
	 * Render a plain text field
	 *
	 * @param Field $field
	 *
	 * @return string
	 */
	public function createPlainTextField(Field $field) {
		return Element::create('span', $field->getValue(), $field->getAttributes());
	}

	/**
	 * Render an icon
	 *
	 * @param array $attributes Its attributes
	 *
	 * @return string
	 */
	public function createIcon($iconType, $attributes = array(), $settings = []) {
		return Element::create('i', null, ['class' => 'fa fa-' .$iconType]);
	}

	/**
	 * Wrap an item to be prepended or appended to the current field
	 *
	 * @param  string $item
	 *
	 * @return string A wrapped item
	 */
	public function placeAround($item) {
		// Render object
		if (is_object($item) and method_exists($item, '__toString')) {
			$item = $item->__toString();
		}

		return Element::create('span', $item)->addClass('add-on');
	}

	/**
	 * Wrap a field with prepended and appended items
	 *
	 * @param  Field $field
	 * @param  array $prepend
	 * @param  array $append
	 *
	 * @return string A field concatented with prepended and/or appended items
	 */
	public function prependAppend($field, $prepend, $append) {
		$class = array();
		if ($prepend) {
			$class[] = 'input-prepend';
		}
		if ($append) {
			$class[] = 'input-append';
		}

		$return = '<div class="'.join(' ', $class).'">';
		$return .= join(null, $prepend);
		$return .= $field->render();
		$return .= join(null, $append);
		$return .= '</div>';

		return $return;
	}

	/**
	 * Wrap a field with potential additional tags
	 *
	 * @param  Field $field
	 *
	 * @return string A wrapped field
	 */
	public function wrapField($field) {
		return Element::create('div', $field)->addClass('controls');
	}

}