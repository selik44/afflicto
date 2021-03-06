@extends('front.layout')


@section('styles')
    <style type="text/css">
        /* Enhance the look of the textarea expanding animation */
        .animated {
            -webkit-transition: height 0.2s;
            -moz-transition: height 0.2s;
            transition: height 0.2s;
        }
        .stars {
            margin: 20px 0;
            font-size: 24px;
            color: #d17581;
        }


    </style>
@stop

@section('title')
    @if($product->manufacturer)
        {{$product->manufacturer->name}} -
    @endif
    {{$product->name}} - {{$product->categories->first()->name}} - @parent
@stop

@if($product->meta_description)
@section('meta_description')
    <meta name="description" content="{{$product->meta_description}}">
@stop
@endif

@if($product->meta_keywords)
@section('meta_keywords')
    <meta name="keywords" content="{{$product->meta_keywords}}">
@stop
@endif

@section('breadcrumbs', Breadcrumbs::render('product', $product, $category))

@section('aside')
    <div class="block store-menu visible-m-up">
        <ul id="store-menu" class="nav vertical fancy">
            <?php
            $c = '';
            if (Request::is($category->getRoot()->getPath())) {
                $c = 'current';
            }
            ?>
            <li>
                <a class="{{{$c}}}" href="{{url($category->getRoot()->slug)}}">{{$category->getRoot()->name}}</a>
            </li>
            <?php
            echo $category->getRoot()->renderMenu('', 3);
            ?>
        </ul>
    </div>
    @parent
@stop

@section('article')
{{--{{ dd($test) }}--}}
    <div class="product-view" data-id="{{$product->id}}" data-variants="{{count($product->getVariants())}}" data-stock="{{$product->stock}}">
        <div class="product-top">
            <div class="product-images col-l-8 col-m-7 col-m-12 tight-left clearfix">
                @if(count($product->images) > 1)
                    <div class="thumbnails">
                        <?php $active = 'active'; ?>
                        @foreach($product->images as $key => $image)
                            <a class="thumbnail {{$active}}" href="#" data-slide="{{$key+1}}" style="background-image: url('{{asset('images/products/' .$image->getThumbnail())}}');"></a>
                            <?php $active = '';?>
                        @endforeach
                    </div>
                @endif

                <div class="slider contain">
                    <div class="container">
                        @foreach($product->images as $key => $image)
                            <div class="slide" style="background-image: url('{{asset('images/products/' .$image->name)}}');"></div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="product-info paper">
                @if($product->manufacturer)
                    @if($product->manufacturer->image)
                        <a class="manufacturer text-center" href="#product-manufacturer-description" style="width: 100%; float: left; padding:1rem">
                            <img src="{{asset('images/manufacturers/' .$product->manufacturer->image->name)}}" alt="{{$product->manufacturer->name}} Logo">
                        </a>
                    @else
                        <a class="manufacturer text-center" href="#product-manufacturer-description" style="width: 100%; float: left; padding:1rem">
                            {{$product->manufacturer->name}}
                        </a>
                    @endif
                @endif

                <header class="header">
                    <h3 class="title end">
                        {{$product->name}}
                    </h3>
                    <h3 class="price end">
                        <strong class="value">{{round($product->getDiscountPrice() * $product->vatgroup->amount)}}</strong>,-
                        @if($product->hasDiscount())
                            <del class="discount">{{round($product->price * $product->vatgroup->amount)}},-</del>
                        @endif
                    </h3>
                </header>

                <?php
                $disabled = '';
                ?>
                @include('front.partial.product-buy-form', ['product' => $product, 'withSizemap' => true])

                <button {{$disabled}}class="huge primary toggle-add-modal-dummy" style="display: none;" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>

                <div class="summary">
                    {!! $product->summary !!}
                </div>
            </div>
        </div>


        <div class="modal fade" id="add-modal-{{$product->id}}">
            <div class="modal-content">
                @include('front.partial.product-buy-form', ['product' => $product, 'modal' => true])
            </div>
        </div>

        <div id="slider-modal" class="modal fade" style="height: 100%">
            <a href="#slider-modal" data-toggle-modal="#slider-modal"></a>
        </div>

        {{---------------------------------------------------------------alex fix---------------------------------------------------------------------}}
        @if(Session::has('review_posted'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5>Your review has been posted!</h5>
            </div>
        @endif


        @if(Session::has('review_removed'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5>Your review has been removed!</h5>
            </div>
        @endif
        {{---------------------------------------------------------------alex fix---------------------------------------------------------------------}}
        <div class="paper product-bottom col-xs-12 tight">
            <ul id="product-tabs" class="nav tabs clearfix">
                @if($product->manufacturer && mb_strlen(trim(strip_tags($product->manufacturer->description))) > 3)
                    <li><a href="#product-manufacturer-description">@lang('store.about') {{$product->manufacturer->name}}</a></li>
                @endif

                @if(mb_strlen(trim(strip_tags($product->description))) > 0)
                    <li><a href="#product-info">@lang('store.product info')</a></li>
                @endif

                @foreach($product->producttabs as $tab)
                    <li><a href="#product-tab-{{$tab->id}}">{{$tab->title}}</a></li>
                @endforeach


                {{--@foreach($product->producttabs as $tab)--}}
                <li><a href="#product-tab-reviews">Reviews</a></li>
                {{--@endforeach--}}

                @if($product->relations->count() > 0)
                    <li><a href="#product-relations">@lang('store.related products')</a></li>
                @endif
            </ul>

            @if($product->manufacturer && mb_strlen(trim(strip_tags($product->manufacturer->description))) > 0)
                <div class="tab" id="product-manufacturer-description">
                    {!! $product->manufacturer->description !!}
                </div>
            @endif

            @if(mb_strlen(trim(strip_tags($product->description))) > 0)
                <div class="tab" id="product-info">
                    {!! $product->description !!}
                </div>
            @endif

            @foreach($product->producttabs as $tab)
                <div class="tab" id="product-tab-{{$tab->id}}">
                    {!! $tab->body !!}
                </div>
            @endforeach

            {{--@foreach($product->reviews as $review)--}}
            {{--<div class="tab" id="product-tab-reviews">--}}
            {{--{!! $tab->body !!}--}}
            {{--</div>--}}
            {{--@endforeach--}}



            {{--                    {{ dd($reviews) }}--}}


            {{--@foreach($reviews as $review)--}}


            <div class="tab" id="product-tab-reviews">
                <div class="row">
                    <div class="col-md-9">
                       
                        <div class="row">
                            <div class="col-md-12">
                            </div>
                        </div>
                        @if(Auth::user())
                        <div class="text-right">
                            <a href="#reviews-anchor" id="open-review-box" class="btn btn-success btn-green">Leave a Review</a>
                        </div>
                        @else
                            <div class="col-md-12">
                                <p><strong>Please log in to leave comments</strong></p>
                            </div>
                        @endif
                        <div class="row" id="post-review-box" >
                            <div class="col-md-12">
                                {!! Form::open() !!}
                                {!! Form::hidden('rating', null, array('id'=>'ratings-hidden')) !!}
                                {!! Form::textarea('comment', null, array('rows'=>'5', 'id'=>'new-review','class'=>'form-control animated','placeholder'=>'Enter your review here...')) !!}
                                <div class="text-right">
                                    <div class="stars starrr" data-rating="{{Input::old('rating',0)}}"></div>
                                    <a href="#reviews-anchor" class="btn btn-danger btn-sm" id="close-review-box"  style="margin-right:10px;">Cancel</a>
                                    <a href="#reviews-anchor" class="btn btn-danger btn-sm" id="save-review-box" onclick="$(this).closest('form').submit()">Save</a>

                                    {{--<button class="btn btn-success btn-lg" type="submit">Save</button>--}}

                                </div>
                                {!!  Form::close() !!}
                            </div>
                        </div>

                        @foreach($reviews as $review)
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    @for ($i=1; $i <= 5 ; $i++)
                                        <span class="glyphicon glyphicon-star{{ ($i <= $review->rating) ? '' : '-empty'}}"></span>
                                    @endfor

                                    {{ $review->user ? $review->user->name : 'Anonymous'}} <span class="pull-right">{{$review->timeago}}</span>

                                    <p>{{$review->comment}}</p>
                                </div>
                            </div>
                        @endforeach
                      

                    </div>

                </div>
            </div>



            @if($product->relations->count() > 0)
                <div class="tab clearfix" id="product-relations">
                    <div class="products-grid">
                        @foreach($product->relations as $related)
                            @include('front.partial.products-block', ['product' => $related, 'withBuyButton' => true])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('scripts')
    @parent

    <script type="text/javascript">
        (function($, window, document, undefined) {
            var slider = $(".product-view .product-images .slider");
            var thumbnails = $(".product-view .product-images .thumbnails");
            var alwaysAllowOrders = <?= ($product->manufacturer && $product->manufacturer->always_allow_orders) ? "true" : "false" ?>;

            //set active product tab
            var tab = $("#product-tabs li").first();
            tab = $(tab.find('a').attr('href'));
            $("#product-tabs").gsTabs("switch", tab);

            //init image slider
            slider.friluftSlider({
                delay: 6000,
                transitionSpeed: 400,
                slideLinks: false,
                stopOnMouseEnter: true,
                startOnMouseLeave: true,
            });

            $(document).ready(function() {
                slider.friluftSlider('reLayout');
            });

            //setup thumbnails
            $(".product-view .product-top .product-images .thumbnails .thumbnail").click(function(e) {
                e.preventDefault();
                var id = $(this).attr('data-slide');
                slider.friluftSlider("goTo", id);
                slider.friluftSlider("stop");
                $(".product-view .product-top .product-images .thumbnails .thumbnail.active").removeClass('active');
                $(this).addClass('active');

                return false;
            });

            slider.on('slider.next', function() {
                var id = slider.data('friluftSlider').currentIndex;

                $('.product-view .product-top .product-images .thumbnails .thumbnail.active').removeClass('active');

                $('.product-images .thumbnails .thumbnail[data-slide="' + id + '"]').addClass('active');
            });

            //setup slider zoom modal
            var sliderModal = $("#slider-modal");
            slider.find('.slide').click(function() {
                console.log('zooming');
                var img = $(this).css('background-image');
                //img = img.replace('url(', '');
                //img = img.replace(')', '');
                sliderModal.find('a').css('background-image', img);

                sliderModal.gsModal('show');
            });

            //setup related products isotope
            // initialize isotope
            function initIsotope() {
                console.log('initializing isotope');
                imagesLoaded(document.querySelector('.product-view .products-grid'), function() {
                    $(".product-view .products-grid").isotope({
                        itemSelector: '.product',
                        layoutMode: 'fitRows',
                        getSortData: {
                            price: '[data-price] parseInt',
                            manufacturer: '[data-manufacturer]',
                        }
                    });
                });
            }
            if ($('#product-tabs li a[href="#product-relations"]').parent('li').hasClass('current')) {
                initIsotope();
            }else {
                $("#product-tabs li a[href='#product-relations']").click(function() {
                    initIsotope();
                });
            }

            //stick the buy button to bottom on mobile
            var callback = function() {
                var btn = $(".product-view .product-top button.buy");
                var dummy = $(".product-view .product-top button.toggle-add-modal-dummy");

                if (btn.hasClass('fixed')) {
                    if ( ! dummy.is(':in-viewport')) {
                        btn.addClass('fixed');
                        $("#footer").css('padding-bottom', (btn.outerHeight() + 24) + 'px');
                        dummy.css({
                            display: 'block',
                            visibility: 'hidden',
                        });
                    }else {
                        btn.removeClass('fixed');
                        $("#footer").css('padding-bottom', '0');
                        dummy.css({
                            display: 'none',
                            visibility: 'auto',
                        });
                    }
                }else {
                    if ( ! btn.is(':in-viewport')) {
                        btn.addClass('fixed');
                        $("#footer").css('padding-bottom', (btn.outerHeight() + 24) + 'px');
                        dummy.css({
                            display: 'block',
                            visibility: 'hidden',
                        });
                    }else {
                        btn.removeClass('fixed');
                        $("#footer").css('padding-bottom', '0');
                        dummy.css({
                            display: 'none',
                            visibility: 'auto',
                        });
                    }
                }
            };

            callback();

            $(window).bind('scroll resize', _.throttle(function() {
                callback();
            }, 50));

            $("#buy-form-{{$product->id}} button.buy").click(function(e) {
                e.preventDefault();

                var variants = parseInt($('.product-view').attr('data-variants'));
                if (variants > 0 && $(window).width() <= 680) {
                    //toggle modal
                    $("#add-modal-{{$product->id}}").gsModal('show');
                }else {
                    $(this).parents('form').trigger('submit');
                }
            });



            // setup buy event on both forms
            var form = $("#buy-form-{{$product->id}}, #add-modal-{{$product->id}} form");

            var cart = $("#header .cart-container");
            form.on('submit', function(e) {
                console.log('submitted!');
                e.preventDefault();

                // show buy modal
                var thumbnail = $('.product-view .product-images .slider .slide').first().css('background-image');
                thumbnail = thumbnail.replace('url(', '');
                thumbnail = thumbnail.replace(')', '');
                var title = $('.product-view .header .title .manufacturer .title').text();
                var price = $('.product-view .header .price .value').first().text();
                showBuyModal(1, thumbnail, title, price);

                $("#add-modal-{{$product->id}}").gsModal('hide');

                if ($("#product-relations").length > 0) {
                    setTimeout(function() {
                        $("#product-tabs").gsTabs('switch', "#product-relations");

                        $("body").animate({
                            scrollTop: $("#product-relations").offset().top
                        }, 2000);
                    }, 300);
                }

                //post form via ajax
                $.post($(this).attr('action'), $(this).serialize(), function(response) {
                    var total = Math.round(parseFloat(response.total));

                    //update free shipping status
                    var left = 1000 - total;
                    if (left > 0) {
                        $("#breadcrumbs .free-shipping-status").show().find('.value').text(left);
                    }else {
                        $("#breadcrumbs .free-shipping-status").hide();
                    }

                    //update cart-toggle status
                    var quantity = response.quantity;
                    $("#header .cart-toggle .quantity").text(response.quantity);
                    $("#header .cart-toggle .total").text(Math.round(parseFloat(response.total)));

                    //update cart table
                    $.get(Friluft.URL + '/api/cart', function(html) {
                        cart.find('.cart-table').replaceWith(html);
                    });
                });
            });
        })($, window, document);
    </script>
@stop