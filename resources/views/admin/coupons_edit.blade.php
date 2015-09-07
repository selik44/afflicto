@extends('admin.layout')

@section('title')
	@lang('admin.edit') - @lang('admin.coupons') - @parent
@stop

@section('header')
	<h3 class="title">@lang('admin.coupon') <small>{{$coupon->admin_name}}</small></h3>
@stop

@section('content')
	{!! Former::open()
	->method('PUT')
	->action(route('admin.coupons.update', $coupon))
	->class('vertical')
	->rules([
		'admin_name' => 'required|max:255',
		'name' => 'required|max:255',
		'discount' => 'required|numeric|max:100|min:0',
		'code' => 'required|max:255',
		'single_use' => 'boolean',
		'roles' => 'array',
	])
	!!}

	{!! Former::text('admin_name')!!}

	{!! Former::text('name')->help('Vises i ordrebekreftelse o.l') !!}

	{!! Former::text('discount')->help('Tall fra 0 til 100') !!}

	{!! Former::text('code') !!}

	{!! Former::select('categories')->multiple()->fromQuery($categories, 'name', 'id')->name('categories[]')->label('categories') !!}

	{!! Former::select('products')->multiple()->fromQuery($products, 'name', 'id')->name('products[]')->label('products') !!}

	{!! Former::select('roles')->multiple()->fromQuery($roles, 'name', 'id')->name('roles[]')->label('roles')->help('Kan denne bare brukes av visse roller?') !!}

	{!! Former::checkbox('single_use') !!}

	{!! Former::checkbox('cumulative')->help('Gir denne rabattkoden avslag på allerede rabatterte produkter?') !!}

	{!! Former::checkbox('enabled') !!}

	{!! Former::checkbox('automatic_deactivation')->label('Deaktiveres automatisk?')->class('toggle-valid-until') !!}

	<div class="valid-until" style="display: {{isset($coupon->valid_until) ? 'block' : 'none'}};">
		{!! Former::date('valid_until')->help('Deaktiver automatisk på gitt dato. (Format: yyyy-mm-dd)') !!}
	</div>
@stop

@section('footer')
	{!! Former::submit('save')->class('large success') !!}
	{!! Former::close() !!}
@stop

@section('scripts')
	@parent

	<script>
		$("form select").chosen({width: '100%'});

		$("form .toggle-valid-until").change(function() {
			if ($(this).is(':checked')) {
				$("form .valid-until").show();
			}else {
				$("form .valid-until").hide();
			}
		});
	</script>
@stop