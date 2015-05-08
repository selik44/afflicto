@extends('admin.layout')

@section('title')
	New Product - @parent
@stop

@section('page')
	<h2>New Product</h2>
	{!!Former::open(route('admin.products.store'))->method('POST')->class('vertical') !!}
		<div class="row">
			<div class="col-m-6">
				{!! $form->name !!}
				{!! $form->slug !!}
				{!! $form->brand !!}
				{!! $form->model !!}
				{!! $form->weight !!}
				{!! $form->summary !!}
				{!! $form->enabled !!}
			</div>
			
			<hr class="hidden-m-up">

			<div class="col-m-6">
				<h4>Attributes</h4>
			</div>
		</div>
		
		<hr>

		<div class="row">
			{!! $form->description !!}
		</div>

		<div class="footer-height-fix"></div>

		<footer id="footer">
			<div class="inner">
				<input type="submit" name="create" value="Create" class="primary end">
				<input type="submit" name="continue" value="Create & Continue" class="secondary end">
				<input type="reset" value="reset" class="end">
			</div>
		</footer>
	{!!Former::close()!!}
@stop

@section('scripts')
	@parent
	<script type="text/javascript">
		var form = $("form");
		
		form.find('#categories-select').chosen();

		form.find('[name="slug"]').autoSlug({other: '[name="name"]'});
	</script>
@stop