<?php

namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Sizemap extends Model
{

	public $timestamps = false;

	protected $table = 'sizemaps';

	public $fillable = ['name', 'image'];

	public function products() {
		return $this->hasMany('Friluft\Product');
	}

}
