<?php

namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Tile extends Model
{
	protected $table = 'tiles';

    public $timestamps = false;

	protected $casts = [
		'options' => 'array',
	];
}
