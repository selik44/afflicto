<?php

namespace Friluft\Http\Controllers\Admin;

use Friluft\Laratables\SearchRelationsFilter;
use Friluft\Laratables\SelectRelationsFilter;
use Friluft\Manufacturer;
use Friluft\Order;
use Friluft\Product;
use Friluft\Review;
use Illuminate\Http\Request;
use Friluft\Http\Requests;
use Friluft\Http\Controllers\Controller;
use Laratable;
use Mail;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($new = false)
    {
        $table = Laratable::make( $new ?
            Review::with('product')->where('approved', '=', 0) :
            Review::with('product'), [
            'admin.reviews.index.id' => 'id',
            'admin.reviews.index.user' => 'user->name',
            'admin.reviews.index.product' => 'product->name',
            'admin.reviews.index.manufacturer' => ['product->manufacturer_id', function($model){
                return isset($model->product) && isset($model->product->manufacturer) ? $model->product->manufacturer->name : "";
            }],
            'admin.reviews.index.status' => 'approved',
            'admin.reviews.index.activated' => ['activated', function($model){
                return $model->approved;
            }],
            'admin.reviews.index.created_at' => ['created_at', function ($model){
                return $model->created_at->diffForHumans();
            }],
            'admin.reviews.index.search' => ['search', function($model) {
                return '<a href="'.route('admin.reviews.show', $model->id).'"><i class="fa fa-search"></i></a>';
            
            }],
        ]);
    
    
        $table->selectable(true);
        $table->filterable(true);
        // $table->setQuery(Review::with('product'));
        $table->registerFilter('product', SearchRelationsFilter::class);
        $table->registerFilter('manufacturer', SelectRelationsFilter::class);
        $table->registerFilter('approved', SelectRelationsFilter::class);
        $table->addFilter('product', 'product' );
        $table->addFilter('product->manufacturer_id', 'manufacturer');
        $manufacturers['*'] = 'all';
        $manufacturers = $manufacturers + Manufacturer::all()->sortBy('name')->pluck('name', 'id')->all();
        $table->filters[1]['instance']->setValues($manufacturers);
        $table->addFilter('approved', 'approved' );
        $table->filters[2]['instance']->setValues(['*' => 'all', '0' => 'no', '1' => 'yes']);
        $filters = $table->buildFilters();
    
        $table->sortable(true, [
            'user',
            'product',
            'approved',
            'created_at',
        ]);
    
    
        return view('admin.reviews_index')
            ->with([
                'table' => $table->render(),
                'filters' => $filters,
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
        return $this->index(true);
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
        $review->load('user', 'product');
        
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
    
    /**
     * Approve single review
     *
     * deprecated
     *
     * @param $review
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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
    
    public function bulkUpdate(Request $request)
    {
        $input = $request->all();
        $reviews = Review::whereIn('id', $input['reviews'])->get();
        
        if (!$reviews->isEmpty()) {
            foreach ($reviews as $review) {
                $review->approved = $input['bulk-status'];
                $review->save();
            }
        }
        return redirect()->back();
    }
}
