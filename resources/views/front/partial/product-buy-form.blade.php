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
    data-variants="{{$product->variants->count()}}"
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
                                @if($product->variants_stock[$value['id']] <= 0)
                                    <option disabled="disabled" value="{{$value['id']}}">
                                        {{$value['name']}}
                                        @if ($product->manufacturer)
                                        @endif
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
						<select name="variant-{{$variant->id}}" data-stock='{!! json_encode($child->variants_stock) !!}'>
							@foreach($variant->data['values'] as $value)
								@if($child->variants_stock[$value['id']] <= 0)
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

    <div class="product-stock">
        <p class="true lead color-success">
            <i class="fa fa-check"></i> @lang('store.in stock')
        </p>

        <p class="false lead color-warning">
            <i class="fa fa-exclamation-triangle"></i> @lang('store.out of stock')
        </p>
    </div>

    <button {{$disabled}}class="huge tertiary buy" type="submit" name="BUY"><i class="fa fa-cart-plus"></i> @lang('store.add to cart')</button>
</form>

@section('scripts')
    @parent
    <script>
        (function($, window, document, undefined) {
            var form = $("#{{$id}}");
            var alwaysAllowOrders = <?= ($product->manufacturer && $product->manufacturer->always_allow_orders) ? "true" : "false" ?>;

			var updateStock;

            if (parseInt(form.attr('data-variants')) > 0) {
				var stock = JSON.parse('{!! json_encode($product->variants_stock) !!}');

				updateStock = function() {
					//get the current stock ID
					var stockID = [];
					form.find(".product-variants .variant").each(function() {
						var select = $(this).find('select');
						stockID.push(select.val());
					});
					stockID = stockID.join('_');

					var stockValue = parseInt(stock[stockID]);

					if (stockValue > 0) {
						console.log('in stock');
						form.find("button.buy").removeAttr('disabled');
						form.find(".product-stock .true").show().siblings('.false').hide();
					}else {
						console.log('NOT in stock');
						form.find("button.buy").attr('disabled', 'disabled');
						form.find(".product-stock .true").hide().siblings('.false').show();
					}
				};

				updateStock();

                //listen for change event on the variant form fields
                form.find(".product-variants .variant select").change(function() {
                    updateStock();
                });
            }else if (form.attr('data-compound') == '1') {

				console.log('is compound');

				updateStock = function() {
					console.log('updating stock...');

					var inStock = true;
					form.find('.product-variants .variant select').each(function() {
						var selected = $(this).val();
						if (selected != null) {
							var stock = JSON.parse($(this).attr('data-stock'));
							if (parseInt(stock[selected]) <= 0) {
								inStock = false;
							}
						}else {
							inStock = false;
						}
					});

					if (inStock) {
						console.log('in stock');
						form.find("button.buy").removeAttr('disabled');
						form.find(".product-stock .true").show().siblings('.false').hide();
					}else {
						console.log('NOT in stock');
						form.find("button.buy").attr('disabled', 'disabled');
						form.find(".product-stock .true").hide().siblings('.false').show();
					}
				};

				updateStock();

				form.find('.product-variants .variant select').change(function() {
					updateStock();
				});

			}else {
                var stockNumber = parseInt(form.attr('data-stock'));
                if (stockNumber > 0) {
                    form.find(".product-stock .true").show().siblings('.false').hide();
                    form.find("button.buy").removeAttr('disabled');
                }else {
                    form.find(".product-stock .true").hide().siblings('.false').show();
                    form.find("button.buy").attr('disabled', 'disabled');
                }
            }
        })(jQuery, window, document);
    </script>
@stop