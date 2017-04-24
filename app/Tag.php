<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Tag
 *
 * @property integer $id
 * @property string $label
 * @property string $icon
 * @property string $color
 * @property boolean $enabled
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Product[] $products
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tag whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tag whereLabel($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tag whereIcon($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tag whereColor($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tag whereEnabled($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tag whereType($value)
 * @property boolean $visible
 * @property float $discount
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tag whereVisible($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tag whereDiscount($value)
 * @mixin \Eloquent
 */
class Tag extends Model {

	public $timestamps = false;

	protected $table = 'tags';

	protected $casts = [
		'visible' => 'boolean',
		'discount' => 'float',
	];

	public function products() {
		return $this->belongsToMany('Friluft\Product');
	}


}
