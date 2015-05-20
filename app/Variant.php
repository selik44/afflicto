<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model {

	public $timestamps = false;

	protected $table = 'variants';

	protected $casts = [
		'data' => 'array',
	];

	protected $fillable = ['name', 'data'];

	public function product() {
		return $this->belongsTo('Friluft\Product');
	}

}
