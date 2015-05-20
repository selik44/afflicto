@extends('admin.layout')


@section('title')
	Tree View - Categories - @parent
@stop


@section('page')
	<h2 class="end">Product Categories</h2>
	<h4 class="subtitle end">Tree View</h4>
	<hr>

    <div class="row">
        <div class="categories-tree col-xs-6 tight-left">
            <ul class="flat sortable root">
                @foreach($categories as $category)
                    {!! $category->renderSortableList() !!}
                @endforeach
            </ul>
        </div>

        <div class="col-xs-6 tight-right">
            <ul class="flat products-list clearfix">

            </ul>
        </div>
    </div>

    <div id="product-categories-modal" class="modal fade center" style="max-width: 300px; height: 50%; min-height: 400px;">
        <a style="font-size: 2rem; padding: 0.2rem" href="#" data-toggle-modal="#product-categories-modal" class="modal-dismiss"><i class="fa fa-close"></i></a>
        <header class="modal-header">
            <h4 class="title"></h4>
        </header>
        <article class="modal-content">
            <select data-placeholder="Select categories" class="product-categories-select" multiple></select>
        </article>
        <footer class="modal-footer" style="flex-grow: 0;">
            <button class="large success save">Save</button>
        </footer>
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
        var products = $(".products-list");
        var productCategoriesModal = $("#product-categories-modal").gsModal();
        productCategoriesModal.find('select.product-categories-select')
                .chosen()
                .css({
                    'z-index': 1000,
                    'overflow': 'visible'
                })
                .next().removeAttr('style');

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

        //product list load listener
        categories.find('.info a.name').click(function() {
            var category = $(this).parents('li').first();
            if (category.hasClass('peek') == false) {
                categories.find('li.peek').removeClass('peek');
                category.addClass('peek');
                var id = category.attr('data-id');

                products.load(Friluft.URL + '/admin/html/category/' + id + '/products');
            }
        });

        //product-list info toggle
        products.on('click', '.product .name', function() {
             $(this).parents('.product').first().find('.product-info').slideToggle();
        });

        //product-list enable/disable
        products.on('change', '.product .product-enabled', function() {
            var selected = $(this).is(':checked');
            var id = $(this).parents('.product').first().attr('data-id');
            $.post(Friluft.URL + '/admin/api/products/' + id + '/setenabled', {'_method': 'PUT', '_token': Friluft.token, 'enabled': selected}, function(response) {
                console.log(response);
            });
        });

        //product-list move
        products.on('click', '.product .product-controls .move', function() {
            var id = $(this).parents('.product').first().attr('data-id');
            console.log('id: ' + id);
            productCategoriesModal.attr('data-id', id);

            $.get(Friluft.URL + '/admin/api/products/' + id + '/categories', function(cats) {
                var select = productCategoriesModal.find('.modal-content .product-categories-select');
                select.html("");

                //insert the categories
                var cat;
                for(i in cats) {
                    cat = cats[i];
                    var selected = '';
                    if (cat.selected) selected = ' selected';
                    select.append('<option' + selected + ' value="' + cat.id + '">' + cat.name +'</option>');
                }
                select.trigger('chosen:updated');

                //show the modal
                productCategoriesModal.gsModal('toggle');

                //focus chosen
                select.find('.chosen-choices .search-field input').focus();
            });
        });

        //product-list move save
        productCategoriesModal.find('.modal-footer .save').click(function() {
            var id = $(this).parents('.modal').first().attr('data-id');
            productCategoriesModal.gsModal('hide');

            var cats = [];
            productCategoriesModal.find('.product-categories-select option:selected').each(function() {
                cats.push($(this).attr('value'));
            });

            console.log('updating categories to:');
            console.log(cats);

            $.post(Friluft.URL + '/admin/api/products/' + id + '/categories', {_token: Friluft.token, categories: cats}, function(response) {
                console.log('updated product categories. Response:');
                console.log(response);
            });
        });



        //get URL fragment
        if (window.location.hash) {
            var hash = window.location.hash;
            if (/#category-[0-9]+/.test(hash)) {
                var id = hash.split('-').pop();
                var cat = categories.find('li[data-id="' + id + '"]');
                cat.addClass('peek');

                //make sure it's visible
                cat.parents('ul.sortable').each(function() {
                    showList($(this));
                });

                products.load(Friluft.URL + '/admin/html/category/' + id + '/products');
            }
        }
	</script>
@stop