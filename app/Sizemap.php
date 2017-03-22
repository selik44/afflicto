<?php

namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Sizemap
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $image
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Sizemap whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Sizemap whereImage($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Sizemap whereName($value)
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
