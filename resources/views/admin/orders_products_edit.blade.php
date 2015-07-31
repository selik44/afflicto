@extends('admin.layout')

@section('title')
    Orders - @parent
@stop

@section('page')
    <h2 class="end">Order #{{$order->id}} - Edit</h2>
    <hr>

    <h4 class="end">Products</h4>
    <table class="table bordered striped">
        <thead>
        <tr>
            <th>Product</th>
            <th>Options</th>
            <th>Quantity</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $id => $item)
            <?php
                if ($item['type'] == 'shipping_fee') continue;
                $id = (int) $item['reference']['id'];
                $options = $item['reference']['options'];
                $model = Friluft\Product::find($id);
            ?>
            <tr class="item"
                data-id="{{$id}}"
                data-discount-rate="{{$item['discount_rate']}}"
                data-name="{{$item['name']}}"
                data-product="{{$model->id}}"
                data-type="physical"
            >
                <td class="product">
                    <a href="{{url($model->getPath())}}" target="_blank">
                     {{$model->name}}
                    </a>
                </td>
                <td class="options">
                    <div class="variants">
                    @foreach($model->variants as $variant)
                        <div class="variant" data-id="{{$variant->id}}">
                            <label for="variant-{{$variant->id}}">{{$variant->name}}
                                <select name="variant-{{$variant->id}}" data-id="{{$variant->id}}">
                                    @foreach($variant->data['values'] as $value)
                                        @if($options['variants'][$variant->id] == $value['name'])
                                            <option selected="selected" value="{{$value['name']}}">{{$value['name']}}</option>
                                        @else
                                            <option value="{{$value['name']}}">{{$value['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    @endforeach
                    </div>
                </td>
                <td class="quantity">
                    <input style="max-width: 80px;" type="text" name="quantity" value="{{$item['quantity']}}">
                </td>
                <td class="actions">
                    <div class="pull-right button-group">
                        <button class="small remove error"><i class="fa fa-close"></i> Remove</button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr>

    <h4 class="end">@lang('store.shipping.shipping')</h4>
    <?php
        $shipping = $order->getShipping();
        $types = ['mail', 'service-pack'];
    ?>
    <table class="shipping">
        <thead>
        <tr>
            <th>Type</th>
            <th>@lang('store.price')</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="type">
                <select>
                    @foreach($types as $type)
                        @if($shipping['type'] == $type)
                            <option selected="selected" value="{{$type}}">@lang('store.shipping.' .$type)</option>
                        @else
                            <option value="{{$type}}">@lang('store.shipping.' .$type)</option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td class="price">
                <div class="input-append input-prepend">
                    <span class="prepended">Kr</span>
                    <input style="max-width: 100px;" type="text" value="{{$shipping['total_price_including_tax']}}">
                    <span class="appended">,-</span>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <hr>

    <button class="success large save">Save</button>
@stop

@section('scripts')
    @parent
    <script>

        var orderID = {{$order->id}};

        $("button.save").click(function() {
            var items = [];

            //add products
            $("table tr.item").each(function() {
                var item = {
                    discount_rate: 0,
                    name: "",
                    quantity: 1,
                    reference: {
                        id: null,
                        options: {
                            variants: {}
                        }
                    },
                    type: 'physical',
                };

                //set product/model id
                item.reference.id = $(this).attr('data-product');
                item.name = $(this).attr('data-name');
                item.quantity = parseInt($(this).find('.quantity input').val());

                //set variants
                $(this).find('.variants .variant').each(function() {
                    var variant_id = $(this).find('select').attr('data-id');
                    var value = $(this).find('select').val();
                    item.reference.options.variants[variant_id] = value;
                });

                items.push(item);
            });

            //add shipping
            var shipping = {
                type: 'shipping_fee',
                reference: null,
                tax_rate: 0,
                name: '',
                total_price_including_tax: 0,
                total_price_excluding_tax: 0,
            };

            shipping.name = $(".shipping .type select").val();
            shipping.total_price_excluding_tax = parseInt($(".shipping .price input").val()) * 100;
            shipping.total_price_including_tax = parseInt($(".shipping .price input").val()) * 100;

            items.push(shipping);

            $.post(Friluft.URL + '/orders/' + orderID + '/edit/products', {items: items, _token: Friluft.token, _method: 'PUT'}, function(response) {
                console.log('response: ');
                console.log(response);
            });
        });
    </script>
@stop