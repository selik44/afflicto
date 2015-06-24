@extends('master')

@section('body')
<div id="front">
	<header id="header">

		<nav id="navigation-top">
			<ul class="inner nav end">
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

		<nav id="navigation">
			<div class="inner">
                <div class="nav-controls">
                    <button class="nav-toggle hidden-m-up end"><i class="fa fa-bars"></i></button>

                    <a href="{{url()}}" class="logo">
                        <img src="{{url('images/' .\Friluft\Store::current()->machine .'.png')}}">
                    </a>

                    <button data-toggle="#cart" class="cart-toggle-top primary end hidden-m-up"><i class="fa fa-shopping-cart"></i> Cart</button>
                </div>
				<div class="nav-contents">
					<ul class="nav vertical fancy end navigation">
						@include('front.partial.navigation_' .\Friluft\Store::current()->machine)
					</ul>
					
					<div class="nav-extra">
						<div class="button-group search-and-cart visible-m">
							<button data-toggle="#search" class="primary"><i class="fa fa-search"></i></button>
							<button data-toggle="#cart" class="primary"><i class="fa fa-shopping-cart"></i></button>
						</div>
						
						
						<form class="inline" id="search" action="{{url('search')}}" method="GET">
                            <div class="input-append">
                                <input required maxlength="100" type="search" name="terms" placeholder="search...">
                                <!--<input type="submit" value="Search" class="primary appended">-->
                                <button class="primary appended"><i class="fa fa-search"></i></button>
                            </div>
						</form>

						@if(\Request::route()->getName() != 'store.checkout')
							<button data-toggle="#cart" class="cart-toggle primary end visible-l-up"><i class="fa fa-shopping-cart"></i> Cart</button>
						@endif
					</div>
				</div>

				@if(\Request::route()->getName() != 'store.checkout')
				<div class="cart-container">
					<div id="cart" style="display: none;">
						<div class="inner">
							@include('front.partial.cart', ['items' => Cart::getItemsWithModels(false), 'total' => Cart::getTotal()])
							<br>
							<a class="button large primary" href="{{route('store.checkout')}}">Checkout</a>
						</div>
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
@stop


@section('scripts')

@parent

<script type="text/javascript">
	$(document).ready(function() {
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
		});

		//toggle nav-dropdowns on large+
		$("#navigation .nav-contents > ul.nav > li > a").mouseenter(function() {
			if ($(window).width() <= 680) return;

			var dropdown = $(this).parent('li').children('.nav-dropdown');
			if (dropdown.hasClass('visible')) return;

			$("#navigation .nav-dropdown.visible").removeClass('visible').stop(true, true).slideUp();
			dropdown.toggleClass('visible').slideDown();
		});

		//leave dropdown
		$("#navigation .nav-dropdown").mouseleave(function() {
			if ($(window).width() <= 680) return;
			$(this).slideUp(150).removeClass('visible');
		});

		//hide all dropdowns that are not visible when resizing
		$(window).resize(_.throttle(function() {
			console.log('resize event');
			$("#navigation .nav-dropdown:not(.visible)").hide();
		}, 100));
	});
</script>

@stop