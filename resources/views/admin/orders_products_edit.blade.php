@extends('admin.layout')

@section('title')
    @lang('admin.products') - @lang('admin.orders') - @parent
@stop

@section('header')
    <h3 class="title">@lang('admin.order') #{{$order->id}}</h3>
@stop

@section('content')
    <div class="row end">
        <h4 class="pull-left end">Products</h4>
        <button id="add-button" class="pull-right large add primary" data-toggle-modal="#add-modal"><i class="fa fa-plus"></i> Add</button>
    </div>

    <table id="products" class="table bordered striped">
        <thead>
        <tr>
            <th>Product</th>
            <th>Options</th>
            <th>Quantity</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <tr id="item-template" class="item">
            <td class="product">
                <span class="name"></span>
            </td>
            <td class="variants">

            </td>
            <td class="quantity">
                <input type="text" value="1">
            </td>
            <td>
                <div class="button-group pull-right remove">
                    <button class="remove small error"><i class="fa fa-close"></i> Remove</button>
                </div>
            </td>
        </tr>

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
                    <span class="name">
                     {{$model->name}}
                    </span>
                </td>
                <td class="options">
                    <div class="variants">
                    @foreach($model->variants as $variant)
                        <div class="variant" data-id="{{$variant->id}}">
                            <label for="variant-{{$variant->id}}">{{$variant->name}}
                                <select name="variant-{{$variant->id}}" data-id="{{$variant->id}}">
                                    @foreach($variant->data['values'] as $value)
                                        @if($options['variants'][$variant->id] == $value['name'])
                                            <option selected="selected" value="{{$value['id']}}">{{$value['name']}}</option>
                                        @else
                                            <option value="{{$value['id']}}">{{$value['name']}}</option>
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
    <table id="shipping" class="shipping">
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

    <div class="modal fade" id="add-modal" style="overflow: visible;width: 400px;">
        <div class="modal-header">
            Add Product
        </div>

        <div class="modal-content" style="overflow: visible;">
            <select class="product">
                @foreach($products as $product)
                    <option value="{{$product->id}}" data-id="{{$product->id}}">{{$product->name}}</option>
                @endforeach
            </select>
            <hr>
            <div class="product-options">
                @foreach($products as $product)
                    <div style="display: none;" class="options variants" data-product="{{$product->id}}">
                        @foreach($product->variants as $variant)
                            <div class="variant" data-id="{{$variant->id}}">
                            <label>{{$variant->name}}
                                <select data-id="{{$variant->id}}">
                                    @foreach($variant->data['values'] as $value)
                                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                                    @endforeach
                                </select>
                            </label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="modal-footer">
            <div class="button-group">
                <button class="primary add"><i class="fa fa-plus"></i> Add</button>
                <button class="cancel" data-toggle-modal="#add-modal">Cancel</button>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <button class="success large save">@lang('admin.save')</button>
@stop

@section('scripts')
    @parent
    <script>
        var orderID = {{$order->id}};

        var itemTemplate = $("#item-template").detach();

        //setup chosen on product selector
        $("#add-modal select.product").chosen({width: '100%'});

        //set visible product options
        $("#add-modal select.product").change(function() {
            $("#add-modal .product-options .options.visible").hide();
            var id = $(this).val();
            $("#add-modal .product-options .options[data-product='" + id + "']").show().addClass('visible');
        });

        //ADD PRODUCT
        $("#add-modal .modal-footer .add").click(function() {
            var id = $("#add-modal select.product").val();
            var name = $("#add-modal select option[data-id='" + id + "']").text();
            var options = {variants: {}};

            $("#add-modal .product-options .options.visible .variant").each(function() {
                var variantID = $(this).attr('data-id');
                var id = $(this).val();
                options.variants[variantID] = id;
            });

            var item = {id: id, name: name, options: options};

            var el = itemTemplate.clone();

            el.attr('data-product', item.id);
            el.attr('data-name', item.name);
            el.attr('data-type', 'physical');
            el.find('.product .name').text(item.name);
            el.find('.quantity input').val(1);
            el.find('.variants').append($("#add-modal .product-options .options.visible .variant"));

            $("#products tbody").append(el);
            el.show();

            $("#add-modal").gsModal('hide');
        });

        //SAVE
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
                    var id = $(this).find('select').val();
                    item.reference.options.variants[variant_id] = id;
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
            shipping.total_price_excluding_tax = parseInt($(".shipping .price input").val());
            shipping.total_price_including_tax = parseInt($(".shipping .price input").val());

            items.push(shipping);

            console.log(items);

            $.post(Friluft.URL + '/admin/orders/' + orderID + '/edit/products', {items: items, _token: Friluft.token, _method: 'PUT'}, function(response) {
                console.log('response: ');
                console.log(response);
            });
        });
    </script>
@stop