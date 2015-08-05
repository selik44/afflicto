@extends('splash')

@section('title')
    404 - @parent
@stop

@section('header')
    <h2><i class="fa fa-warning"></i> 404</h2>
@stop

@section('content')
    <h4>Whops! Den siden finnes ikke.</h4>
    <form class="vertical" id="search" action="{{route('search')}}" method="GET">
        <div class="input-append input-prepend">
            <a style="min-width: 100px;" href="{{url()}}" class="button"><i class="fa fa-chevron-left"></i> Forsiden</a>
            <input required maxlength="100" type="search" name="terms" placeholder="Hva leter du etter?">
            <button class="primary appended"><i class="fa fa-search"></i></button>
        </div>
    </form>
@stop