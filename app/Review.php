<?php
/**
 * Created by PhpStorm.
 * User: darinx
 * Date: 02.03.17
 * Time: 13:12
 */

namespace Friluft;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



/**
 * Friluft\Review
 *
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property int $rating
 * @property string $comment
 * @property bool $approved
 * @property bool $spam
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read mixed $timeago
 * @property-read \Friluft\Order $order
 * @property-read \Friluft\Product $product
 * @property-read \Friluft\User $user
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review approved()
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review notSpam()
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review spam()
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereComment($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereRating($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereSpam($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Friluft\Review whereUserId($value)
 * @mixin \Eloquent
 */
class Review extends Model{
    
    protected $table = 'reviews';
    
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'title',
        'approved',
        'spam'
    ];

    public static $rules = [
        'title' => 'required|min:2',
        'comment'=>'required|min:10',
    ];
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    // public function order()
    // {
    //     return $this->belongsTo('Friluft\Order');
    // }
    
    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }
    public function scopeSpam($query)
    {
        return $query->where('spam', true);
    }
    public function scopeNotSpam($query)
    {
        return $query->where('spam', false);
    }
    // Attribute presenters
    public function getTimeagoAttribute()
    {
        $date = \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
        return $date;
    }
    // this function takes in product ID, comment and the rating and attaches the review to the product by its ID, then the average rating for the product is recalculated
    public function storeReviewForProduct($productID, $comment, $rating)
    {
        $product = Product::find($productID);
        $this->user_id = \Auth::user()->id;
        $this->comment = $comment;
        $this->rating = $rating;
        $this->approved = 0;
        $product->reviews()->save($this);
        // recalculate ratings for the specified product
        //$product->recalculateRating($rating);
    }


    public function approve(){

        $this->approved = 1;
        $this->save();

    }


    public function allReviews($query)
    {

        return $query->where('comment', '!=', null);

    }

    public function userReviews(){

        $userReviews = $this->where('user_id', '=', 1);
        return $userReviews;

    }



}