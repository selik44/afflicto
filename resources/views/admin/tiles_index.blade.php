@extends('admin.layout')

@section('title')
    @lang('admin.tiles') - @lang('admin.design') - @parent
@stop

@section('content')
    <div class="row end">
        <h3 class="pull-left end" style="margin-right: 2rem">Tiles</h3>
    </div>
    <hr class="small">

    <div class="tiles">

    </div>

    <div class="tile-template tile">

    </div>
@stop

@section('scripts')
<script>
(function($, window, document, undefined) {

    var tiles = $(".tiles");
    var template = $(".tile-template").detach().removeClass('tile-template');
})(jQuery, window, document);
</script>
@stop