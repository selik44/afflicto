<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\Permission
 *
 * @property integer $id
 * @property string $machine
 * @property string $name
 * @property string $description
 * @property-read \Illuminate\Database\Eloquent\Collection|\Friluft\Role[] $roles
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Permission whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Permission whereMachine($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Permission whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Permission whereDescription($value)
 * @mixin \Eloquent
 */
class Permission extends Model {

	public $timestamps = false;
	protected $table = 'permissions';

	public function roles() {
		return $this->belongsToMany('Friluft\Role');
	}

}