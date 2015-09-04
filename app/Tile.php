<?php

namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Tile
 *
 * @property integer $id
 * @property string $type
 * @property string $content
 * @property string $width
 * @property string $height
 * @property string $options
 * @property integer $order
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tile whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tile whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tile whereWidth($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tile whereHeight($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tile whereOptions($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Tile whereOrder($value)
 */
class Tile extends Model
{
	protected $table = 'tiles';

    public $timestamps = false;

	protected $casts = [
		'options' => 'array',
	];
}
