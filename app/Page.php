<?php

namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Page
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string $options
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Page whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Page whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Page whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Page whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Page whereOptions($value)
 * @mixin \Eloquent
 */
class Page extends Model
{

	protected $table = 'pages';

	public $timestamps = false;

	protected $fillable = [
		'title','slug','content'
	];

	protected $casts = [
		'options' => 'json',
	];

}
