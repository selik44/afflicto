<?php

namespace Friluft;

use Illuminate\Database\Eloquent\Model;
/**
 * Friluft\Sizemap
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products
 * @mixin \Eloquent
Add a comment to this line
 */


class Sizemap extends Model
{

	public $timestamps = false;

	protected $table = 'sizemaps';

	public $fillable = ['name', 'image'];

	public function products() {
		return $this->hasMany('Friluft\Product');
	}

}
