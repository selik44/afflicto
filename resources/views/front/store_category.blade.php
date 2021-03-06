@extends('front.layout')

@section('title')
	{{{$category->name}}} - @parent
@stop

@if($category->meta_description)
@section('meta_description')
    <meta name="description" content="{{$category->meta_description}}">
@stop
@endif

@if($category->meta_keywords)
@section('meta_keywords')
    <meta name="keywords" content="{{$category->meta_keywords}}">
@stop
@endif

@section('breadcrumbs', Breadcrumbs::render('category', $category))

	<?php
        $mostExpensive = 0;
        foreach($products as $product) {
            if ($product->price > $mostExpensive) {
                $mostExpensive = ceil($product->price * $product->vatgroup->amount);
            }
        }
	?>

@section('scripts')
	@parent
	<script type="text/javascript">
		var grid = $(".products-grid");
		var options = $(".products-grid-options");
		var priceSlider = options.find('.filters .price-filter .price-slider');
		var priceSliderMinValue = options.find('.filters .price-filter .min-value');
		var priceSliderMaxValue = options.find('.filters .price-filter .max-value');

		var manufacturersSelect = options.find('.filters .manufacturers-filter select');

		priceSlider.noUiSlider({
			start: [0, {{$mostExpensive}}],
			step: 10,
			range: {
				min: 0,
				max: {{$mostExpensive}}
			}
		});

		priceSlider.on({
			slide: function() {
				var value = priceSlider.val();
				priceSliderMinValue.text(parseInt(value[0]));
				priceSliderMaxValue.text(parseInt(value[1]));
			},

			change: function() {
				updateFilter();
			}
		});

        $(".products-grid-header .sort select").change(function() {
            var sort = $(this).val();
            var asc = false;
            if (sort == '----') {
                sort = null;
            }else if (sort == 'price-asc') {
                sort = 'price';
                asc = true;
            }else if (sort == 'price-desc'){
                sort = 'price';
                asc = false;
            }

            //sort
            grid.isotope({
                sortBy: sort,
                sortAscending: asc,
            });
        });

		// manufacturers select
		manufacturersSelect.chosen().next().removeAttr('style').css('width', '100%');

		manufacturersSelect.change(function(e) {
			var filter = manufacturersSelect.val();
			if (filter == '*') {
				filter = null;
			}

			updateFilter();
		});

        $(".products-grid-options .variants .filter select").change(function() {
            console.log('variants filter changed');
            updateFilter();
        })

		function updateFilter() {
			grid.isotope({
				filter: function() {
					var price = parseInt($(this).attr('data-price'));
					var manufacturer = $(this).attr('data-manufacturer');

                    //selected filter values
                    var filters = [];
                    $(".products-grid-options .variants .filter").each(function() {
                        filters[$(this).attr('data-variant')] = $(this).find('select').val();
                    });

                    var value;
                    for(var name in filters) {
                        value = filters[name];
                        if (value == '*') continue;
                        var supported = $(this).attr('data-variant-' + name);

                        if (supported !== undefined && supported !== null) {
                            supported = supported.split(',');
                            var inArray = false;
                            for(var i in supported) {
                                var val = supported[i];
                                if (val == value) inArray = true;
                            }
                            if ( ! inArray) {
                                return false;
                            }else {
                            }
                        }
                    }

					if (price < priceSlider.val()[0]) return false;

					if (price > priceSlider.val()[1]) return false;

					if (manufacturersSelect.val() !== '*' && manufacturer !== manufacturersSelect.val()) return false;

					return true;
				}
			});
		};

		// initialize isotope
        grid.isotope({
            getSortData: {
                price: '[data-price] parseInt',
            },
        });
	</script>
@stop

@section('aside')
    <div class="block">
        <div class="module">
            <div class="module-header">
                <h6>Filter</h6>
            </div>
            <div class="products-grid-options clearfix module-content">
                <div class="filters clearfix">
                    <div class="filter price-filter">
                        <div class="header">
                            <h5 class="pull-left end">@lang('store.price')</h5>
                        </div>
                        <div class="values">
                            <div class="pull-left min-value">0</div>
                            <div class="pull-right max-value">{{$mostExpensive}}</div>
                        </div>
                        <div class="control">
                            <div class="price-slider"></div>
                        </div>
                    </div>

                    <div class="filter manufacturers-filter">
                        <h5>@lang('store.manufacturer')</h5>
                        <select name="manufacturers-select" class="manufacturers-select">
                            <option value="*">@lang('store.all')</option>
                            @foreach($manufacturers as $m)
                                <option value="{{$m->id}}">{{$m->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="variants">
                        @foreach($variants as $name => $values)
                            <div class="filter variant" data-variant="{{$name}}">
                                <h5>{{ucwords($name)}}</h5>
                                <select name="variant-{{str_replace(' ', '-', $name)}}">
                                    <option value="*">@lang('store.all')</option>
                                    @foreach($values as $value)
                                        <option value="{{$value}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @parent
@stop

@section('article')

    <?php
        $banner = $category->getBanner();
    ?>

    @if($banner)
        <div class="category-banner">
            <img src="{{asset('images/' .$banner->name)}}">
        </div>
    @endif

    <div class="paper clearfix products-grid-header">
        <h4 class="title end pull-left">
            {{$category->name}}
        </h4>
        <div class="sort pull-right">
            <h4 for="sort">@lang('store.sort.sort')</h4>
            <select name="sort">
                <option value="none">----</option>
                <option value="price-asc">@lang('store.sort.price ascending')</option>
                <option value="price-desc">@lang('store.sort.price descending')</option>
                @if(false)
                <option value="manufacturer">@lang('store.manufacturer')</option>-->
                @endif
            </select>
        </div>
    </div>

	@include('front.partial.products-grid', ['withMenu' => true])
@stop