@extends('master')

@section('body')
<div id="front">
	<header id="header">

		<nav id="nav-top">
			<ul class="clearfix inner nav end">
				<li><a href="#">Help</a></li>
				<li><a href="#">Account</a></li>
				<li><a href="#">Contact</a></li>
                @if(Auth::user())
                    @if (Auth::user()->role->has('admin.access'))
                        <li><a href="{{route('admin.dashboard')}}">Admin</a></li>
                    @endif
                @endif
				<li class="pull-right"><a href="#"><i class="fa fa-globe"></i> English</a></li>
			</ul>
		</nav>

		<nav id="nav" class="clearfix">
			<div class="inner">
                <div class="nav-controls">
                    <button class="nav-toggle hidden-m-up"><i class="fa fa-bars"></i></button>

                    <a href="{{url()}}" id="logo">
                        <img src="{{url('images/' .\Friluft\Store::current()->machine .'.png')}}">
                    </a>

                    <button data-toggle="#cart" class="cart-toggle-top pull-right primary end hidden-m-up"><i class="fa fa-shopping-cart"></i> Cart</button>
                </div>
				<div class="nav-contents">
					<ul class="nav end navigation">
						@include('front.partial.navigation_' .\Friluft\Store::current()->machine)
					</ul>
					
					<div class="nav-extra">
						<div class="button-group search-and-cart hidden-l-up">
							<button data-toggle="#search" class="primary"><i class="fa fa-search"></i> Search</button>
							<button data-toggle="#cart" class="primary"><i class="fa fa-shopping-cart"></i> Cart</button>
						</div>
						
						
						<form class="inline" id="search" action="{{url('search')}}" method="GET">
                            <div class="input-append">
                                <input required maxlength="100" type="search" name="terms" placeholder="search...">
                                <!--<input type="submit" value="Search" class="primary appended">-->
                                <button class="primary appended"><i class="fa fa-search"></i></button>
                            </div>
						</form>
						
						<button data-toggle="#cart" class="cart-toggle primary end visible-l-up"><i class="fa fa-shopping-cart"></i> Cart</button>
					</div>
				</div>

				<div class="cart-container">
					<div id="cart" style="display: none;">
						<div class="inner">
							@include('front.partial.cart', ['items' => Cart::getItemsWithModels(false), 'total' => Cart::getTotal()])
						</div>
					</div>
				</div>

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
			<div class="inner">
				@yield('breadcrumbs')
			</div>
		</div>
	@endif
	
	<div id="alerts">
		@include('partial.alerts')
	</div>
	

	<div id="page" class="clearfix">
		<div class="inner clearfix">

				@if(isset($aside) && $aside)
					<hr class="hidden-m-up">
					<aside id="aside" class="tight col-m-4 col-l-3">
						<div class="inner">
							@section('aside')
								@include('front.partial.aside')
							@show
						</div>
					</aside>
				@endif

				<?php
					$c = (isset($aside) && $aside) ? 'col-m-8 col-l-9' : '';
				?>
				<article id="article" class="tight {{$c}}">
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
@stop


@section('scripts')

@parent

<script type="text/javascript">
	$(document).ready(function() {
		//make the header fixed
		var header = $("#header");
		var front = $("#front");
		var nav = $("#nav");
		var navTop = $("#nav-top");

		//add 'scrolled' class when scrolling past navTop
		$(window).scroll(_.throttle(function() {
			var scroll = $('body').scrollTop();
			if (scroll >= navTop.height()) {
				$('body').addClass('scrolled');
			}else {
				$('body').removeClass('scrolled');
			}
		}, 50));


		function showDropdown(dd, duration, callback) {
			dd.stop().slideDown(duration, callback).addClass('visible');
		}

		function hideDropdown(dd, duration, callback) {
			dd.stop().slideUp(duration, callback).removeClass('visible');
		}


		//nav dropdown toggles
		var ul = $('#header #nav ul.navigation');
		ul.find('> li > a').click(function(e) {
			if ($(window).width() <= 960) {
				e.preventDefault();
			}
			var dd = $(this).parent('li').find('> .nav-dropdown');

			var current = nav.find('.nav-dropdown.visible');
			if (current.length > 0) {
				if (current.is(dd)) return;

				hideDropdown(current, 150, function() {
					showDropdown(dd, 300);
				});
				return;
			}

			showDropdown(dd);
		});

		ul.find('> li > a').mouseenter(function() {
			if ($(window).width() <= 960) {
				e.preventDefault();
			}
			var dd = $(this).parent('li').find('> .nav-dropdown');

			var current = nav.find('.nav-dropdown.visible');
			if (current.length > 0) {
				if (current.is(dd)) return;

				hideDropdown(current, 150, function() {
					showDropdown(dd, 300);
				});
				return;
			}

			showDropdown(dd);
		});

		$('#header .nav-dropdown').mouseleave(function() {
			if ($(this).hasClass('visible')) {
				hideDropdown($(this), 200);
			}
		});


		//search, dropdown & cart display
		var search = $("#search");
		var cart = $("#cart");
		$("[data-toggle='#search']").click(function() {
			search.stop().slideToggle().toggleClass('visible');
		});

		$("[data-hide='#search']").click(function() {
			search.removeClass('visible').stop().slideUp();
		});

		$("[data-show='#search']").click(function() {
			search.stop().slideToggle().toggleClass('visible');
			cart.removeClass('visible').stop().slideUp();
		});

		
		$("[data-toggle='#cart']").click(function() {
			if (cart.hasClass('visible')) {
				//hide cart
				cart.stop().slideUp().removeClass('visible');
			}else {
				var dropdown = $("#nav .nav-dropdown");
				if (dropdown[0] != null) {
					dropdown.hide();
				}
				cart.stop().slideDown().addClass('visible');
			}
		});

		$("[data-hide='#cart']").click(function() {
			cart.stop().slideUp().removeClass('visible');
		});

		$("[data-show='#cart']").click(function() {
			cart.stop().slideDown().addClass('visible');
			search.stop().slideUp().removeClass('visible');
		});





		//navigation toggle for sm and xs
		var navContents = $("#nav .nav-contents");
		$("#header .nav-toggle").click(function(e) {
			navContents.toggleClass('visible');
			if (navContents.hasClass('visible')) {
				$("body").addClass('with-menu');
				$("#nav-top").show();
			}else {
				$("body").removeClass('with-menu');
			}
		});

	});
</script>

@stop