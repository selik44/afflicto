<?php

namespace Friluft;

use Illuminate\Database\Eloquent\Model;

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
