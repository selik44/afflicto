@extends('master')

@section('body')
<div id="front">
	<header id="header">

		<nav id="navigation-top">
			<ul class="inner nav end">

				<li><a href="#">Help</a></li>
				<li><a href="#">Contact</a></li>
                @if(Auth::user())
                    @if (Auth::user()->role->has('admin.access'))
                        <li><a href="{{route('admin.dashboard')}}">Admin</a></li>
                    @endif
                @endif

                <?php
                    $langs = ['en' => 'English', 'no' => 'Norsk', 'se' => 'Svensk'];
                    $currentLang = \App::getLocale();
                ?>

                <li class="pull-right dropdown language">
                    <a href="#"><i class="fa fa-globe"></i> {{$langs[$currentLang]}} <i class="fa fa-chevron-down"></i></a>
                    <ul>
                        @foreach($langs as $lang => $label)
                            @if($currentLang != $lang)
                                <li><a href="{{url()}}/{{$lang}}">{{$label}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </li>

                @if(Auth::user())
                    <li class="pull-right"><a href="{{route('user')}}">@lang('store.account')</a></li>
                @else
                    <li class="pull-right"><a href="{{route('user')}}">@lang('store.login')</a></li>
                @endif
			</ul>
		</nav>

		<nav id="navigation">
			<div class="inner">
                <div class="nav-controls">
                    <button class="nav-toggle hidden-l-up end"><i class="fa fa-bars"></i></button>

                    <a href="{{url()}}" class="logo">
                        <img src="{{url('images/' .\Friluft\Store::current()->machine .'.png')}}">
                    </a>

                    <button class="cart-toggle primary end hidden-l-up"><i class="fa fa-shopping-cart"></i> <span class="text">Cart</span></button>
                </div>
				<div class="nav-contents">
					<ul class="nav vertical fancy end navigation">
						@include('front.partial.navigation_' .\Friluft\Store::current()->machine)
					</ul>
					
					<div class="nav-extra">
						<form class="inline" id="search" action="{{route('search')}}" method="GET">
                            <div class="input-append">
                                <input required maxlength="100" type="search" name="terms" placeholder="search...">
                                <!--<input type="submit" value="Search" class="primary appended">-->
                                <button class="primary appended"><i class="fa fa-search"></i></button>
                            </div>
						</form>

						@if(\Request::route()->getName() != 'store.cart' && \Request::route()->getName() != 'store.checkout' && \Request::route()->getName() != 'store.success')
							<button data-toggle="#cart" class="cart-toggle primary end visible-l-up"><i class="fa fa-shopping-cart"></i> Cart</button>
						@endif
					</div>
				</div>

                @if(\Request::route()->getName() != 'store.cart' && \Request::route()->getName() != 'store.checkout' && \Request::route()->getName() != 'store.success')
				<div class="cart-container" style="display: none;">
					<div class="inner">
						@include('front.partial.cart-table', ['items' => Cart::getItemsWithModels(false), 'total' => Cart::getTotal()])

                        <footer class="cart-footer">
                            <a class="button large primary pull-right" href="{{route('store.cart')}}">Checkout</a>
                        </footer>
					</div>
				</div>
				@endif

			</div>
		</nav>
	</header>
	
	@if(isset($slider) && $slider)
		<div id="slider" class="clearfix">
			<div class="inner clearfix">
				@yield('slider')
			</div>
		</div>
	@endif

	@if(!isset($breadcrumbs) || $breadcrumbs == true)
		<div id="breadcrumbs">
			<div class="inner clearfix">
                <div class="pull-left crumbs">
				    @yield('breadcrumbs')
                </div>

                <?php
                $display = 'none';
                $left = 1000;
                $total = Cart::getTotal();
                if ($total > 0) {
                    $left = 1000 - $total;
                    if ($left > 0) {
                        $display = 'block';
                    }
                }
                ?>

                <div class="pull-right free-shipping-status" style="display: {{$display}}">
                    <i class="fa fa-exclamation-circle color-warning"></i> Du mangler <span class="value">{{$left}}</span>,- for å få gratis frakt.
                </div>
			</div>
		</div>
	@endif
	
    @include('partial.alerts')

    @if(isset($intro) && $intro)
        <div id="intro" class="clearfix">
            <div class="inner">
                @yield('intro')
            </div>
        </div>
    @endif

	<div id="page" class="clearfix">
		<div class="inner clearfix">
            @if(isset($aside) && $aside)
                <aside id="aside">
                    <hr class="hidden-m-up">

                    <div class="inner">
                        @section('aside')
                            @include('front.partial.aside')
                        @show
                    </div>
                </aside>
            @endif

            <article id="article">
                <div class="inner">
                    @yield('article')
                </div>
            </article>
		</div>
	</div>

	<footer id="footer">
		<div class="inner tower">
			<div class="clearfix">
				@section('footer')
					<div class="col-m-5 col-sm-6">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maiores, sit quam autem, quae blanditiis, omnis totam exercitationem in incidunt eum voluptatibus laborum. Blanditiis quae, incidunt accusantium! Deserunt quidem vel, ipsam amet eveniet nesciunt placeat iusto quis, magnam temporibus saepe cum. Optio quam voluptates natus iure esse totam, inventore dolor dicta, laudantium mollitia. Voluptates ex, aliquam suscipit asperiores, hic ab molestias.</p>
					</div>
					<div class="col-m-4 col-sm-3 col-xs-6">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus accusamus error eos tempora earum quaerat. Architecto reiciendis error perspiciatis consectetur accusamus nisi voluptatum distinctio veniam nam, sequi labore eaque. Laudantium.</p>
					</div>
					<div class="col-m-3 col-sm-3 col-xs-6">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim adipisci similique alias debitis, soluta mollitia, nobis sequi tempora sit temporibus.</p>
					</div>
				@show
			</div>

			<div class="inner copyright tower">
				<div class="col-xs-4 text-left">
					<small class="muted">Page rendered in {{round((microtime(true) - LARAVEL_START) * 1000)}}ms</small>
				</div>
				<div class="col-xs-4 text-center">
					<small class="muted">COPYRIGHT &copy; 2015 - {{\Friluft\Store::current()->name}}</small>
				</div>
				<div class="col-xs-4 text-right">
					<small class="muted">Multi-Store CMS & Design By <a href="#">Gentle Fox Studios</a></small>
				</div>
			</div>
		</div>

	</footer>
</div>

<div id="menu-overlay">

</div>

<div id="buy-modal" class="modal fade center">
    <div class="modal-content">
        <h1 class="end header">@lang('store.product added to cart')</h1>
        <hr>

        <div class="info">
            <h2 class="quantity">
                <span class="value">1</span> x
            </h2>

            <img width="100" src="http://lorempixel.com/100/100/abstract" class="thumbnail">

            <div class="description">
                <h5 class="title">Title</h5>
                <h3 class="price">179,-</h3>
            </div>
        </div>

        <hr>

        <div class="footer">
            <div class="end button huge primary continue" data-toggle-modal="#buy-modal">@lang('store.continue shopping')</div>
            <a href="{{route('store.cart')}}" class="end button huge success">@lang('store.to checkout')</a>
        </div>
    </div>
</div>
@stop


@section('scripts')
@parent
<script type="text/javascript">

    var showBuyModal;

	$(document).ready(function() {
        //init buy modal
        showBuyModal = function(quantity, thumbnail, title, price) {
            var m = $("#buy-modal");
            m.find('.info .quantity .value').html(quantity);
            m.find('.info .thumbnail').attr('src', thumbnail);
            m.find('.info .description .title').html(title);
            m.find('.info .description .price').html(price + ",-");
            $("#buy-modal").gsModal("show");
        };


		//make the nav controls fixed on scroll
		var navTop = $("#navigation-top");

		//add 'scrolled' class when scrolling past navTop
		$(window).scroll(_.throttle(function() {
			var scroll = $('body').scrollTop();
			if (scroll >= navTop.height()) {
				$('body').addClass('scrolled');
			}else {
				$('body').removeClass('scrolled');
			}
		}, 50));


		//toggle cart cotnainer
		$("#header .cart-toggle").click(function() {
			var cart = $("#header .cart-container");
			cart.stop(false, false).slideToggle().toggleClass('visible');

			$(this).toggleClass('active');
		});


		//toggle nav-contents on small devices
		$("#header .nav-toggle").click(function() {
			$(this).toggleClass('active');
			$("#header .nav-contents").toggleClass('visible');
			$("body").toggleClass('with-menu');
		});

		//untoggle nav-contents when clicking on menu-overlay
		$("#menu-overlay").click(function() {
			$("body").removeClass('with-menu');
			$("#header .nav-contents").removeClass('visible');
			$("#header .nav-toggle").removeClass('active');
		});

		//toggle nav sub-menus on small
		$("#navigation .nav .navitem-dropdown-toggle").click(function() {
			$(this).toggleClass('active');
			$(this).parent().toggleClass('visible-small').children('.nav-dropdown, ul').first().slideToggle();
			$(this).parent().toggleClass('visible-small').children('.nav-dropdown, ul').first().css({height: '0px', display: 'block'}).animate({height: 'auto'}, {
				progress: function() {
					var li = $(this).parents('li').first();

					li.css('display', 'none');
					li.outerHeight();
					li.css('display', 'flex');

					console.log('animating');
				}
			});
		});

		function getDropdown() {
			var dd = $("#navigation .nav-dropdown.visible");
			if (dd.length > 0) return dd;
			return null;
		};

		//toggle nav-dropdowns on large+
		$("#navigation .nav-contents > ul.nav > li > a").mouseenter(function() {
			if ($(window).width() <= 680) return;

			var dropdown = $(this).parent('li').children('.nav-dropdown');
			var current = getDropdown();

			if (current != null) {
				$("#navigation .nav-contents .nav-dropdown").each(function() {
					if ($(this).is(dropdown) == false) {
						$(this).stop(false, false).removeClass('visible').slideUp(150);
					}
				});

				dropdown.addClass('visible').stop(false, false).slideDown(250);
			}else {
				dropdown.addClass('visible').stop(false, false).slideDown(250);
			}
		});

		//leave dropdown
		$("#navigation .nav-dropdown").mouseleave(function() {
			if ($(window).width() <= 680) return;
			$(this).stop(false, false).slideUp(150).removeClass('visible');
		});

		//hide all dropdowns that are not visible when resizing
		$(window).resize(_.throttle(function() {
			$("#navigation .nav-dropdown:not(.visible)").hide();
		}, 100));

		//DEV MODE
		//$(".nav-contents ul li:nth-child(3)").find('.nav-dropdown').slideDown(50).addClass('visible');
	});
</script>

@stop