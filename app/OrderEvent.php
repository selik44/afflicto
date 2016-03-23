<?php namespace Friluft;

use Illuminate\Database\Eloquent\Model;

/**
 * Friluft\OrderEvent
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $comment
 * @property integer $order_id
 * @property-read \Friluft\Order $order
 * @method static \Illuminate\Database\Query\Builder|\Friluft\OrderEvent whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\OrderEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\OrderEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\OrderEvent whereComment($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\OrderEvent whereOrderId($value)
 */
class OrderEvent extends Model {

	protected $table = 'orderevents';

    public $timestamps = true;

	protected $dates = [
		'created_at',
	];

    public function order() {
        return $this->belongsTo('Friluft\Order');
    }

}
