<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

	public $timestamps = false;
	protected $table = 'roles';

	protected $fillable = ['name','machine'];

	public function permissions()
	{
		return $this->belongsToMany('Friluft\Permission');
	}

	public function users()
	{
		return $this->hasMany('Friluft\User');
	}

	public function has($permissions)
	{
		if ($this->machine === 'admin') return true;

		# get permissions
		if (func_num_args() > 1) $permissions = func_get_args();
		if (is_string($permissions)) $permissions = explode(',', trim($permissions, ','));

		if (!is_array($permissions)) return false;

		# array_diff should return an empty array if we have all the permissions.
		return !array_diff($permissions, $this->permissions->lists('machine'));
	}

}
