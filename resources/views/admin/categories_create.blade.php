@extends('admin.layout')

@section('title')
	New Category - @parent
@stop

@section('page')
	<h2>New Category</h2>
	<form class="vertical" action="{{url('admin/categories/store')}}" method="post">
		<label for="name">Name <span class="color-error">*</span>
			<input type="text" name="name" maxlength="255" required>
		</label>

		<label for="slug">Slug <span class="color-error">*</span>
			<input type="text" name="slug" maxlength="255" required>
		</label>

		<label for="parent">Parent
			<select id="categories-select" name="parent">
				<option value="null">None</option>
				@foreach($categories as $cat)
					<option value="{{$cat->id}}">{{$cat->name}}</option>
				@endforeach
			</select>
		</label>
		
		<div class="footer-height-fix"></div>

		<footer id="footer">
			<div class="inner">
				<div class="button-group">
					<input type="submit" class="primary" name="create" value="Create">
					<input type="submit" class="secondary" name="continue" value="Create & Continue">
				</div>
			</div>
		</footer>

	</form>
@stop

@section('scripts')
	@parent
	<script type="text/javascript">
		var form = $("form");
		
		form.find('#categories-select').chosen();

		form.find('[name="slug"]').autoSlug({other: '[name="name"]'});
	</script>
@stop