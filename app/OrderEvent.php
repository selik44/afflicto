<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

class OrderEvent extends Model {

	protected $table = 'orderevents';

    public $timestamps = true;

    public function order() {
        return $this->belongsTo('Friluft\Order');
    }

}
