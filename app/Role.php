<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Role
 *
 * @property integer $id
 * @property string $name
 * @property string $machine
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\User[] $users
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Role whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Role whereMachine($value)
 * @mixin \Eloquent
 */
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
		if ($this->machine === 'superadmin') return true;

		# get permissions
		if (func_num_args() > 1) $permissions = func_get_args();
		if (is_string($permissions)) $permissions = explode(',', trim($permissions, ','));

		if (!is_array($permissions)) return false;

		# array_diff should return an empty array if we have all the permissions.
		return !array_diff($permissions, $this->permissions->lists('machine')->toArray());
	}

}
