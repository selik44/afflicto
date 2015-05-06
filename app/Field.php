<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Field extends Model {

	protected $table = 'fields';

	protected $casts = [
		'options' => 'array',
	];

	public function attribute() {
		return $this->belongsTo('Friluft\Attribute');
	}

	public function render($name, $value = '') {
		$str = '';

		$options = $this->options;

		if ($this->type == 'textfield') {
			$str .= '<input type="text" name="' .$name .'" value="' .$value .'">'
		}
		else if ($this->type == 'dropdown') {
			$str .= '<select name="' .$name .'">';
				foreach($options['values'] as $label => $value) {
					$str .= '<option value="' .$value .'"> ' .$label .'</option>';
				}
			$str .= '</select>';
		}

		return $str;
	}

}
