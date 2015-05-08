<div class="actions button-group">
	@if($editable)
		<a class="button" href="{{$editableURL}}"><i class="fa fa-pencil"></i></a>
	@endif

	@if($destroyable)
		<form action="{{$destroyableURL}}" method="POST">
			<input type="hidden" name="_token" value="{{csrf_token()}}">
			<input type="hidden" name="_method" value="DELETE">
			<button onclick="submit();"><i class="fa fa-trash-o"></i></button>
		</form>
	@endif
</div>