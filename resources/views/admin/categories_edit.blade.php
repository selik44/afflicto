@extends('admin.layout')

@section('title')
	{{$category->name}} - Categories - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.categories') - {{$category->name}}</h3>
@stop

@section('content')
	<form class="vertical" action="{{route('admin.categories.update', $category)}}" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="_token" value="{{csrf_token()}}">
		<input type="hidden" name="_method" value="PUT">
		<label for="name">Name <span class="color-error">*</span>
			<input type="text" name="name" value="{{$category->name}}" maxlength="255" required>
		</label>

		<label for="slug">Slug <span class="color-error">*</span>
			<input type="text" name="slug" value="{{$category->slug}}" maxlength="255" required>
		</label>

        <label for="discount">@lang('admin.discount')
            <div class="input-append">
                <input type="text" name="discount" value="{{$category->discount}}">
                <span class="appended">%</span>
            </div>
        </label>
        <br>

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

        <hr>

        <h4>Banner</h4>
        @if($category->banner)
            <img src="{{asset('images/' .$category->banner->name)}}" alt="Category Banner">
        @endif
        <input type="file" name="banner">
@stop

@section('footer')
        <input type="submit" class="primary" value="Save">
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