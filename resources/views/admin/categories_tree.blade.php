@extends('admin.layout')


@section('title')
	Tree View - Categories - @parent
@stop


@section('page')
	<h2 class="end">Product Categories</h2>
	<h4 class="subtitle end">Tree View</h4>
	<hr>
	
	<div class="categories-tree col-xs-12 tight">
		<ul class="flat sortable root">
			@foreach($categories as $category)
				{!! $category->renderSortableList() !!}
			@endforeach
		</ul>
	</div>

	<div class="footer-height-fix"></div>

	<footer id="footer">
		<div class="inner">
			<form id="categories-save-form" action="{{url('admin/categories/tree')}}" method="POST">
				<input type="hidden" name="_token" value="{{csrf_token()}}">
				<input type="hidden" name="_method" value="PUT">
				<input type="hidden" name="tree">
			</form>
			<button class="save-tree-button">Save</button>
		</div>
	</footer>
@stop


@section('scripts')
	@parent

	<script>
		var categories = $(".categories-tree");
		var root = $(".categories-tree > ul.sortable");
		var sortable = $(".categories-tree ul.sortable");
		var form = $("#categories-save-form");

		//init form
		$('.save-tree-button').click(function(e) {
			var serialize = function(ul) {
				var tree = [];
				var order = 0;

				if (ul.length > 0) {
					ul.find('> li').each(function() {
						tree.push({
							id: $(this).attr('data-id'),
							order: order,
							children: serialize($(this).find('> ul.sortable').first()),
						});
						order++;
					});
				}

				return tree;
			};

			var tree = serialize($(".categories-tree > ul.sortable").first());

			var json = JSON.stringify(tree);

			form.find('[name="tree"]').val(json);

			form.trigger('submit');
		});


		//init sortable
		sortable.sortable({
			connectWith: '.categories-tree ul.sortable',
			handle: '> .item > .handle',
			forcePlaceholderSize: true,
			placeholder: '<li class="placeholder"></li>'
		});

		//hide sub-lists
		root.find('ul.sortable').hide();

		//show empty sub-lists by default
		root.find('ul.sortable').each(function() {
			var items = $(this).children('li');
			if (items.length == 0) {
				showList(this);
			}
		});

		function showList(sortable) {
			var li = $(sortable).parent('li');
			li.addClass('visible');

			li.find('> .item .arrow i')
				.removeClass('fa-chevron-down')
				.addClass('fa-chevron-up');

			$(sortable).slideDown();
		};

		function hideList(sortable) {
			var li = $(sortable).parent('li');
			li.removeClass('visible');

			li.find('> .item .arrow i')
				.removeClass('fa-chevron-up')
				.addClass('fa-chevron-down');

			$(sortable).slideUp();
		};

		//setup toggle-able lists
		root.find('.item .arrow').click(function() {
			var li = $(this).parents('li').first();
			if (li.hasClass('visible')) {
				hideList(li.children('ul'));
			}else {
				showList(li.children('ul'));
			}
		});
	</script>
@stop