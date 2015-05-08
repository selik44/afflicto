@extends('admin.layout')

@section('title')
	{{$category->name}} - Categories - @parent
@stop

@section('page')
	<h2 class="end">Categories</h2>
	<h4 class="subtitle">{{{$category->name}}}</h4>

	<hr>
	
	<form class="vertical" action="{{route('admin.categories.update', $category)}}" method="POST">
		<input type="hidden" name="_token" value="{{csrf_token()}}">
		<input type="hidden" name="_method" value="PUT">
		<label for="name">Name <span class="color-error">*</span>
			<input type="text" name="name" value="{{$category->name}}" maxlength="255" required>
		</label>

		<label for="slug">Slug <span class="color-error">*</span>
			<input type="text" name="slug" value="{{$category->value}}" maxlength="255" required>
		</label>

		<label for="parent">Parent
			<select id="categories-select" name="parent_id">
				<option value="null">None</option>
				@foreach($categories as $cat)
					@if($category->parent != null && $category->parent->id == $cat->id)
						<option selected value="{{$cat->id}}">{{$cat->name}}</option>
					@else
						<option value="{{$cat->id}}">{{$cat->name}}</option>
					@endif
				@endforeach
			</select>
		</label>
		
		<div class="footer-height-fix"></div>

		<footer id="footer">
			<div class="inner">
				<div class="button-group">
					<input type="submit" class="primary" value="Save">
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