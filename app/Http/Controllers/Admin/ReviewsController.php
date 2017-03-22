<?php

namespace Friluft\Http\Controllers\Admin;

use Friluft\Order;
use Friluft\Product;
use Friluft\Review;
use Illuminate\Http\Request;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Laratable;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $table = Laratable::make(Review::query(), [
            '#' => 'id',
            'user_id' => 'user_id',
            'product_id' => 'product_id',
            'product_name' => ['product_name', function($model) {
            
                return  Product::where('id', '=', $model->product_id)->first()->name;
            }],
            'comment' => 'comment',
            'rating' => 'rating',
            'approved' => 'approved',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'confirm' => ['product_name', function($model) {
            
                return '<form action="'.route('admin.reviews.approve' , $model->id).'" method="post">
                            <input type="hidden" name="_token" value="'. csrf_token() .'">
                            <input type="hidden" name="approved" value="1">
                            <button type="submit">Approve</button>
                         </form>';
            
            }],
        ]);
    
    
        $table->editable(true, url('admin/reviews/{id}/edit'));
        $table->destroyable(true, url('admin/reviews/{id}'));
    
    
        $table->sortable(true, [
            'user_id','product_id', 'created_at','updated_at', 'rating', 'approved'
        ]);
    
    
        return view('admin.reviews_index')
            ->with([
                'table' => $table->render(),
                'pagination' => $table->paginator->render(),
            ]);
    }
    
    /**
     * Display a listing of the resource.
     * Filter non approved Reviews
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexNew(){
        $table = Laratable::make(Review::where('approved', '=', 0), [
            '#' => 'id',
            'user_id' => 'user_id',
            'product_id' => 'product_id',
            'product_name' => ['product_name', function($model) {
                
                return  Product::where('id', '=', $model->product_id)->first()->name;
            }],
            'comment' => 'comment',
            'rating' => 'rating',
            'approved' => 'approved',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'confirm' => ['product_name', function($model) {
                
                return '<form action="'.route('admin.reviews.approve' , $model->id).'" method="post">
                            <input type="hidden" name="_token" value="'. csrf_token() .'">
                            <input type="hidden" name="approved" value="1">
                            <button type="submit">Approve</button>
                         </form>';
                
            }],
        ]);
        
        
        $table->editable(true, url('admin/reviews/{id}/edit'));
        $table->destroyable(true, url('admin/reviews/{id}'));
        
        
        $table->sortable(true, [
            'user_id','product_id', 'created_at','updated_at', 'rating', 'approved'
        ]);
        
        
        return view('admin.reviews_index')
            ->with([
                'table' => $table->render(),
                'pagination' => $table->paginator->render(),
            ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $review = Review::findOrFail($id);
        return view('admin.reviews_show')->with(compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $review = Review::findOrFail($id);
        return view('admin.reviews_edit')->with(compact('review'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $input = $request->all();
    
        $review->update($input);
        
        return redirect()->route('admin.reviews.show', $review->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function approveReview($review)
    {
        
        $review = Review::findOrFail($review);
        $review->approve();
        
        
        $requestSend = Order::RequestDate($review->user_id);
        $code = $this->createCoupone();
        
        if(count($requestSend) > 0){
            
            foreach ($requestSend as $request){
                
                $email = $request->user->email;
                
                $order = Order::findOrFail($request->id);
                $order->couponeActivated();
                
                #send coupone
                Mail::send('emails.store.feedback_review', ['coupone' => $code], function($mail) use($email){
                    $mail->to($email)->subject('Discount coupone');
                    
                });
                
            }
        }
        
        return redirect()->back()->with('review_approved', true);
        
    }
    
    
}
