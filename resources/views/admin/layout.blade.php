@extends('master')

@section('title')
	Admin - @parent
@stop

@section('body')
	<div id="admin">
		<header id="header">
            <ul id="nav-top" class="nav end">
                <li>
                    <a href="/">View Site</a>
                </li>
                @foreach(\Friluft\Store::all() as $store)
                    @if($store->name == \Friluft\Store::current()->name)
                        <li class="pull-right current"><a href="http://{{$store->host}}.tk/{{Request::path()}}">{{$store->name}}</a></li>
                    @else
                        <li class="pull-right"><a href="http://{{$store->host}}.tk/{{Request::path()}}">{{$store->name}}</a></li>
                    @endif
                @endforeach
            </ul>

            <div id="nav" class="clearfix">
                @include('admin.partial.nav')
            </div>

		</header>
		
		<div id="page">
            <header class="header">
                @yield('header')
            </header>
            <section class="content">
                @include('partial.alerts')
                @yield('content')
            </section>

            <footer class="footer">
                @yield('footer')
            </footer>
		</div>
	</div>
@stop

@section('scripts')
    @parent

    <script>
        (function($, window, document, undefined) {
            var content = $("#page > .content");
            var footer = $("#page > .footer");
            $(window).resize(_.debounce(function() {
                content.css('margin-bottom', footer.outerHeight() + 'px');
            }, 50));

            content.css('margin-bottom', footer.outerHeight() + 'px');
        })(jQuery, window, document);
    </script>
@stop