@extends('master')

@section('meta_description')
    <meta name="description" content="{{\Friluft\Setting::whereMachine('meta_description')->first()->value}}">
@stop

@section('meta_keywords')
    <meta name="keywords" content="{{\Friluft\Setting::whereMachine('meta_keywords')->first()->value}}">
@stop

@section('body')
    <?php
        $background = '';
        $img = Friluft\Image::whereType('background')->first();
        if ($img) {
            $background = asset('images/' .$img->name);
        }
    ?>
<div id="front" style="background-image: url('{{$background}}')">
	<header id="header">

		<nav id="navigation-top">
			<ul class="inner nav end">
				<li><a href="{{url()}}/kontakt-oss">Kontakt</a></li>
				<li><a href="{{url()}}/konkurranser">Konkurranser</a></li>
				<li><a href="{{url()}}/nyhetsbrev">Nyhetsbrev</a></li>
				<li><span class="divider" style="width: 2rem"></span></li>
                @if(Auth::user())
                    @if (Auth::user()->role->has('admin.access'))
                        <li><a href="{{route('admin.dashboard')}}">Admin</a></li>
                    @endif
                @endif

                <?php
                    $langs = ['en' => 'English', 'no' => 'Norsk', 'se' => 'Svensk'];
                    $currentLang = \App::getLocale();
                ?>

                <li class="pull-right dropdown language" style="display: none;">
                    <a href="#language-dropdown" class="dropdown-toggle"><i class="fa fa-globe"></i> {{$langs[$currentLang]}} <i class="fa fa-caret-down"></i></a>
                    <ul id="language-dropdown" class="dropdown-menu align-right">
                        @foreach($langs as $lang => $label)
                            @if($currentLang != $lang)
                                <li><a href="{{url()}}/{{$lang}}">{{$label}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </li>


                <li class="pull-right dropdown account">
                    <a href="#account-dropdown" class="dropdown-toggle">@lang('store.account') <i class="fa fa-caret-down"></i></a>
                    <ul id="account-dropdown" class="dropdown-menu align-right">
                        @if(Auth::user())
                            <li><a href="{{route('user')}}">@lang('store.my account')</a></li>
                            <li><a href="{{route('user.logout')}}">@lang('store.log out')</a></li>
                        @else
                            <li><a href="{{route('user.login')}}">@lang('store.log in')</a></li>
                            <li><a href="{{route('user.register')}}">@lang('store.register')</a></li>
                        @endif
                    </ul>
                </li>
			</ul>
		</nav>

		<nav id="navigation">
			<div class="inner">
                <div class="nav-controls">
                    <button class="nav-toggle hidden-l-up end"><i class="fa fa-bars"></i></button>

                    <a href="{{url()}}" class="logo">
                        <img src="{{url('images/' .\Friluft\Store::current()->machine .'.png')}}">
                    </a>

                    @if(\Request::route()->getName() != 'store.cart' && \Request::route()->getName() != 'store.checkout' && \Request::route()->getName() != 'store.success')
                        <button class="cart-toggle tertiary hidden-l-up" data-toggle="#cart"><i class="fa fa-shopping-cart"></i>  (<span class="quantity">{{Cart::quantity()}}</span>) <span class="total">{{round(Cart::getTotal())}}</span>,-</button>
                    @endif
                </div>
				<div class="nav-contents">
					<ul class="nav vertical fancy end navigation">
						@include('front.partial.navigation_' .\Friluft\Store::current()->machine)
					</ul>

					<div class="nav-extra">
						<form class="inline" id="search" action="{{route('search')}}" method="GET">
                            <div class="input-append">
                                <input required maxlength="100" type="search" name="terms" placeholder="@lang('store.search')...">
                                <button class="primary addon"><i class="fa fa-search"></i></button>
                            </div>
						</form>

						@if(\Request::route()->getName() != 'store.cart' && \Request::route()->getName() != 'store.checkout' && \Request::route()->getName() != 'store.success')
                            <button class="cart-toggle tertiary visible-l-up" data-toggle="#cart"><i class="fa fa-shopping-cart"></i> (<span class="quantity">{{Cart::quantity()}}</span>) <span class="total">{{round(Cart::getTotal())}}</span>,-</button>
						@endif

                        <div id="buy-modal" style="display: none;">
                            <h4 class="end header">@lang('store.product added to cart')</h4>
                            <hr>

                            <div class="info">
                                <h3 class="quantity">
                                    <span class="value">1</span> x
                                </h3>

                                <img width="100" src="http://lorempixel.com/100/100/abstract" class="thumbnail">

                                <div class="description">
                                    <h6 class="title">Title</h6>
                                    <h4 class="price">179,-</h4>
                                </div>
                            </div>

                            <div class="footer">
                                <div class="end button huge primary continue" data-toggle-modal="#buy-modal">@lang('store.continue shopping')</div>
                                <a href="{{route('store.checkout')}}" class="end button huge success">@lang('store.to checkout')</a>
                            </div>
                        </div>
					</div>
				</div>

                @if(\Request::route()->getName() != 'store.cart' && \Request::route()->getName() != 'store.checkout' && \Request::route()->getName() != 'store.success')
				<div class="cart-container" style="display: none;">
					<div class="inner">
						@include('front.partial.cart-table', ['items' => Cart::getItemsWithModels(false), 'total' => Cart::getTotal(), 'withCheckoutButton' => true, 'withShipping' => false, 'shipping' => Cart::getShipping(), 'withTotal' => true])
					</div>
				</div>
				@endif

			</div>
		</nav>
	</header>

    @include('front.partial.slogan')

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
                    @section('breadcrumbs')
                        {!! Breadcrumbs::renderIfExists() !!}
                    @show
                </div>

                <?php
                $display = 'none';
                $left = 1400;
                $total = round(Cart::getTotal());
                if ($total > 0) {
                    $left = 1400 - $total;
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
                <div class="inner clearfix">
                    @yield('article')
                </div>
            </article>
        </div>
	</div>

	<footer id="footer">
		<div class="inner tower">
			<div class="clearfix">
				@section('footer')
                    <div class="col-m-4">
                        {!! Friluft\Setting::whereMachine('footer_1_content')->first()->value !!}
                    </div>
                    <div class="col-m-4">
                        {!! Friluft\Setting::whereMachine('footer_2_content')->first()->value !!}
                    </div>
                    <div class="col-m-4">
                        {!! Friluft\Setting::whereMachine('footer_3_content')->first()->value !!}
                    </div>
				@show
			</div>

			<div class="inner copyright tower">
				<div class="col-xs-6 text-left">
					<small class="muted">COPYRIGHT &copy; 2015 - {{\Friluft\Store::current()->name}}</small>
				</div>
				<div class="col-xs-6 text-right">
					<small class="muted">Multi-Store CMS & Design By <a href="http://gentlefox.net" target="_BLANK">Gentle Fox Studios</a></small>
				</div>
			</div>
		</div>

	</footer>
</div>

<div id="menu-overlay">

</div>

@if(Session::has('popup_newsletter'))
	@include('front.partial.popup')
@endif

@stop


@section('scripts')
@parent
<script type="text/javascript">

    window.showBuyModal = null;

	$(document).ready(function() {
        //init buy modal
        window.showBuyModal = function(quantity, thumbnail, title, price) {
            //unquote thumbnail
            thumbnail = thumbnail.replace('"', '');
            thumbnail = thumbnail.replace('\'', '');
            thumbnail = thumbnail.replace('"', '');

            var m = $("#buy-modal");
            m.find('.info .quantity .value').html(quantity);
            m.find('.info .thumbnail').attr('src', thumbnail);
            m.find('.info .description .title').html(title);
            m.find('.info .description .price').html(price + ",-");
            m.slideDown();
            showBuyModalTimeout = setTimeout(function() {
                m.slideUp();
            }, 2300);
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


		//toggle cart container
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
            var dropdown = $(this).parent().children('ul, .nav-dropdown').first();
            dropdown.css('height', 'auto').slideToggle();
        });

        /*
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
		*/

		function getDropdown() {
			var dd = $("#navigation .nav-dropdown.visible");
			if (dd.length > 0) return dd;
			return null;
		};

		//toggle nav-dropdowns on large+
		$("#navigation .nav-contents > ul.nav > li > a").mouseenter(function() {
			if ($(window).width() <= 960) return;

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
			if ($(window).width() <= 960) return;
			$(this).stop(false, false).slideUp(150).removeClass('visible');
		});

		//hide all dropdowns that are not visible when resizing
		$(window).resize(_.throttle(function() {
			$("#navigation .nav-dropdown:not(.visible)").hide();
		}, 100));
	});
</script>

<script type="text/javascript">
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-66227713-1', 'auto');
	ga('send', 'pageview');
</script>

@stop
