<?php

namespace Friluft;

use Former;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

	public $timestamps = false;

	protected $fillable = [
		'category','type','machine'
	];

	public function getValueAttribute() {
		$v = $this->attributes['value'];
		switch($this->type) {
			case "boolean":
				return ($v == '1') ? true : false;
			break;
			case "text":
				return (string) $v;
			break;
			case "array":
				return json_decode($v, true);
			break;
		}

		return $v;
	}

	public function setValueAttribute($v) {
		switch($this->type) {
			case "boolean":
				$v = ($v == '1') ? true : false;
				break;
			case "string":
				$v = (string) $v;
				break;
			case "array":
				$v = json_encode($v);
				break;
		}

		$this->attributes['value'] = $v;
	}

	public function getField() {
		$m = $this->machine;
		if ($this->type == 'boolean') {
			return Former::checkbox($m)->value('1');
		}else if ($this->type == 'html') {
			return Former::textarea($m)->value($this->attributes['value'])->class('wysiwyg');
		}else if ($this->type == 'array') {
			return Former::textfield($m)->value($this->attributes['value']);
		}else if ($this->type == 'color') {
			return Former::color($m)->value($this->attributes['value']);
		}else {
			return Former::text($m)->value($this->value);
		}
	}

}
