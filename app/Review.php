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



class Review extends Model{


    public function getCreateRules()
    {
        return array(
            'comment'=>'required|min:10',
//            'rating'=>'required|integer|between:1,5'
        );
    }



    // Relationships
    public function user()
    {
        return $this->belongsTo('Friluft\User');
    }
    public function product()
    {
        return $this->belongsTo('Product');
    }
    public function order()
    {
        return $this->belongsTo('Friluft\Order');
    }
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


}