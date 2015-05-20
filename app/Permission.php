<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

	public $timestamps = false;
	protected $table = 'permissions';

	public function roles() {
		return $this->belongsToMany('Friluft\Role');
	}

}