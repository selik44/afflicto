<?php namespace Friluft;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, SoftDeletes;

	public $timestamps = true;

	protected $table = 'users';

	protected $fillable = ['firstname', 'lastname', 'email'];

	protected $hidden = ['password', 'remember_token'];

	public function role() {
		return $this->belongsTo('Friluft\Role');
	}

	public function orders() {
		return $this->hasMany('Friluft\Order');
	}

	public function addresses() {
		return $this->hasMany('Friluft\Address');
	}

	public function getNameAttribute() {
		return $this->attributes['firstname'] .' ' .$this->attributes['lastname'];
	}

	public function setNameAttribute($value) {
		$name = explode(' ', $value, 1);
		if (count($name) > 1) {
			$this->attributes['firstname'] = $name[0];
			$this->attributes['lastname'] = $name[1];
		}
	}

}
