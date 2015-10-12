@extends('admin.layout')

@section('title')
	@lang('admin.new') - @lang('admin.receivals') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.receivals') <small>@lang('admin.new')</small></h3>
@stop

@section('content')
	{!! Former::open()
		->action(route('admin.receivals.store'))
		->method('POST')
	!!}

	<div class="row tower">
		<div class="col-xs-7 tight-left product">
			{!!
			Former::select('product')->label(null)->fromQuery(\Friluft\Product::all())
			!!}

			<div class="variant">

			</div>
		</div>

		<div class="col-xs-3">
			<div id="quantity">
				{!!
				Former::number('quantity')->value(1)->min(1)->label(null)
				!!}
			</div>
		</div>

		<div class="col-xs-2 tight-right">
			<button class="add-product"><i class="fa fa-plus"></i> Add</button>
		</div>
	</div>

	<hr/>

	{!! Former::submit(trans('admin.save'))->class('large primary') !!}
	{!! Former::close() !!}
@stop

@section('scripts')
	@parent

	<script>
		var table = $("#receivalsTable");
		var form = $("form");
		var product = form.find('[name="product"]');
		var quantity = form.find('[name="quantity"]').css('width', '100%');
		product.chosen().next().removeAttr('style').css('width', '100%');

		product.change(function() {
			var id = $(this).val();


		});
	</script>
@stop