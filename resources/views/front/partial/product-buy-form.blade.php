<?php

    $disabled = '';

    if ($product->variants->count() == 0 && ! $product->isCompound()) {
        if ($product->stock <= 0) {
            $disabled = 'disabled="disabled" ';
        }
    }

    $id = 'buy-form-' .$product->id;
    if(isset($modal) && $modal) {
        $id = 'buy-modal-' .$product->id;
    }
?>

<form
    class="vertical"
    action="{{route('api.cart.store')}}"
    method="POST"
    data-variants="{{count($product->getVariants())}}"
    data-stock="{{$product->stock}}"
    id="{{$id}}"
	data-compound="{{(int) $product->isCompound()}}"
>
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <input type="hidden" name="product_id" value="{{$product->id}}">

    @if(count($product->variants) > 0 && ! $product->isCompound())
        @if(count($product->variants) == 1)
            <div class="product-variants">
                @foreach($product->variants as $variant)
                    <div class="variant" data-id="{{$variant->id}}">
                        <label for="variant-{{$variant->id}}">{{$variant->name}}</label>
                        <select name="variant-{{$variant->id}}">
                            @foreach($variant->data['values'] as $value)
                                @if($product->variants_stock[$value['id']] <= 0 && $product->getExpectedArrival() == null)
                                    <option disabled="disabled" value="{{$value['id']}}">
                                        {{$value['name']}}
                                    </option>
                                @else
                                    <option value="{{$value['id']}}">{{$value['name']}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        @else
            <div class="product-variants">
                @foreach($product->variants as $variant)
                    <div class="variant" data-id="{{$variant->id}}">
                        <label for="variant-{{$variant->id}}">{{$variant->name}}</label>
                        <select name="variant-{{$variant->id}}">
                            @foreach($variant->data['values'] as $value)
                                <option value="{{$value['id']}}">{{$value['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
        @endif
	@elseif($product->isCompound())
		<div class="product-variants">
			@foreach($product->getChildren() as $child)
				@foreach($child->variants as $variant)
					<div class="variant" data-id="{{$variant->id}}">
						<label for="variant-{{$variant->id}}">{{$variant->name}} ({{$child->name}})</label>
						<select name="variant-{{$variant->id}}" data-variant="{{$variant->id}}" data-stock='{!! json_encode($child->variants_stock) !!}' data-product="{{$child->id}}" data-availability="{{$child->getAvailability()}}">
							@foreach($variant->data['values'] as $value)
								@if($child->variants_stock[$value['id']] <= 0 && $child->getExpectedArrival() == null)
									<option disabled="disabled" value="{{$value['id']}}">
										{{$value['name']}}
									</option>
								@else
									<option value="{{$value['id']}}">{{$value['name']}}</option>
								@endif
							@endforeach
						</select>
					</div>
				@endforeach
			@endforeach
		</div>
	@endif

	@if($product->sizemap && isset($withSizemap) && $withSizemap)
		<h4 class="sizemap">
			<a href="#" data-toggle-modal="#sizemap-modal" class="button large">Finn Riktig Størrelse</a>
		</h4>

		<div class="modal fade" id="sizemap-modal">
			<div class="modal-content">
				<img src="{{asset('images/sizemaps/' .$product->sizemap->image)}}" alt="Størrelse-kart">
			</div>
		</div>
	@endif

    <div class="product-availability">
        <p class="bad lead color-error">
			<i class="fa fa-exclamation-triangle"></i> @lang('store.out of stock')
        </p>

	    @if($product->isCompound())
			@foreach($product->getChildren() as $child)
				@if($child->getExpectedArrival() != null)
					<p class="warning lead color-warning child-{{$child->id}}">
						Forventet ankomst: om {{\Friluft\Utils\LocalizedCarbon::diffForHumans($child->getExpectedArrival(), null, true)}}.
					</p>
			    @endif
			@endforeach
		@else
			<?php
		        $arrival = $product->getExpectedArrival();
			?>

			@if($arrival != null)
				<p class="warning lead color-warning">
					Forventet ankomst: om {{\Friluft\Utils\LocalizedCarbon::diffForHumans($arrival, null, true)}}.
				</p>
			@endif
		@endif

		<p class="good lead color-success">
			<i class="fa fa-check"></i> @lang('store.in stock')
		</p>
    </div>

    <button {{$disabled}}class="huge tertiary buy" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
</form>

@section('scripts')
    @parent
    <script>
        (function($, window, document, undefined) {
            var form = $("#{{$id}}");
			var availability = <?= $product->getAvailability() ?>;
			var updateStock;

	        console.log('availability is: ' + availability);

			var enableBuy = function(enable) {

				if (enable) {
					form.find("button.buy").removeAttr('disabled');
				}else {
					form.find("button.buy").attr('disabled', 'disabled');
				}
			};

			var setAvailability = function(num) {
				var n = parseInt(num);

				// good, warning or bad?
				var str;
				if (n == 0) {
					str = 'bad';
				}else if (n == 1) {
					str = 'warning';
				}else {
					str = 'good';
				}

				// toggle
				form.find('.product-availability .' + str).show().siblings().hide();
			}

	        //HAS VARIANTS but NOT compound
            if (parseInt(form.attr('data-variants')) > 0 && form.attr('data-compound') == '0') {
	            var stock = JSON.parse('{!! json_encode($product->variants_stock) !!}');

	            updateStock = function () {
		            console.log('updating stock for variants...');
		            //get the current stock ID
		            var stockID = [];
		            form.find(".product-variants .variant").each(function () {
			            var select = $(this).find('select');
			            stockID.push(select.val());
		            });
		            stockID = stockID.join('_');

		            var stockValue = parseInt(stock[stockID]);

		            if (stockValue > 0) {
			            //in stock
			            enableBuy(true);
			            setAvailability(2);
		            } else {
			            console.log('not in stock!');
			            console.log('availability is ' + availability);
			            if (availability > 0) {
				            enableBuy(true);
				            setAvailability(1);
			            } else {
				            enableBuy(false);
				            setAvailability(0);
			            }
		            }
	            };

	            updateStock();

	            //listen for change event on the variant form fields
	            form.find(".product-variants .variant select").change(function () {
		            updateStock();
	            });
            }else if (form.attr('data-compound') == '1' && form.attr('data-variants') == '0') {
	            // IS COMPOUND but NO VARIANTS
	            var stockValue = parseInt(form.attr('data-stock'));

	            console.log('stock is : ' + stockValue);

	            if (stockValue > 0) {
		            //in stock
		            enableBuy(true);
		            setAvailability(2);
	            } else {
		            if (availability > 0) {
			            enableBuy(true);
			            setAvailability(1);
		            } else {
			            enableBuy(false);
			            setAvailability(0);
		            }
	            }
            }else if (form.attr('data-compound') == '1') {
	            // IS COMPOUND + VARIANTS
				updateStock = function() {
					console.log('updating stock (compound)...');
					var inStock = true;
					var availability = 2;
					var warningID = null;

					form.find('.product-variants .variant select').each(function() {
						var selected = $(this).val();
						var stock = JSON.parse($(this).attr('data-stock'));
						if (parseInt(stock[selected]) <= 0) {
							inStock = false;

							var variantAvailability = parseInt($(this).attr('data-availability'));
							if (variantAvailability < availability) {
								availability = variantAvailability;
								warningID = $(this).attr('data-product');
							}
						}
					});

					if (inStock) {
						enableBuy(true);
						setAvailability(2);
						form.find(".product-stock .true").show().siblings('.false').hide();
					}else {
						if (availability == 1) {
							console.log('warning on product id ' + warningID);

							form.find('.product-availability .warning.child-' + warningID).show().siblings().hide();
							enableBuy(true);
						}else if (availability == 2) {
							setAvailability(2);
							enableBuy(true);
						}else {
							enableBuy(false);
							setAvailability(0);
						}
					}
				};

				updateStock();

				form.find('.product-variants .variant select').change(function() {
					updateStock();
				});

			}else {
                setAvailability(availability);
				if (availability > 0) {
					enableBuy(true);
				}else {
					enableBuy(false);
				}
            }
        })(jQuery, window, document);
    </script>
@stop