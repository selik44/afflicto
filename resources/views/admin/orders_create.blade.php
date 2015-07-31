@extends('admin.layout')

@section('title')
    Create - Orders - @parent
@stop

@section('page')
    <h2 class="end">New Order</h2>
    <hr>

    <label for="user-select">@lang('admin.user')
    <select id="user-select">
        @foreach($users as $user)
            <option value="{{$user->id}}">{{$user->name}}</option>
        @endforeach
    </select>
    </label>

    <hr>

    <div class="module" id="items">
        <div class="module-header clearfix">
            <h6 class="pull-left end">Items</h6>
            <button id="add-button" class="pull-right large add primary" data-toggle-modal="#add-modal"><i class="fa fa-plus"></i> Add</button>
        </div>

        <div class="module-content">
            <table class="orders bordered">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Quantity</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr class="template item">
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
                </tbody>
            </table>
        </div>
    </div>

    <hr>

    {!! Former::open()
    ->action(route('admin.orders.store'))
    ->method('POST')
     !!}
    {!! Former::hidden('items') !!}
    {!! Former::hidden('user_id') !!}
    <button class="large success create">Create</button>
    {!! Former::close() !!}

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
                    <div style="display: none;" class="options" data-product="{{$product->id}}">
                        @foreach($product->variants as $variant)
                            <label>{{$variant->name}}
                            <select data-id="{{$variant->id}}" class="variant variant-{{$variant->id}}">
                                @foreach($variant->data['values'] as $value)
                                    <option value="{{$value['name']}}">{{$value['name']}}</option>
                                @endforeach
                            </select>
                            </label>
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

@section('scripts')
    @parent
    <script>
        //init user select
        $("#user-select").chosen({width: '100%'});

        //get item template
        var itemTemplate = $("table tr.item.template").detach();

        //save
        $("form").submit(function(e) {
            var items = [];

            //get items
            $("table tr.item").each(function() {
                var item = {reference: {id: 0, options: {variants: {}}}};
                item.reference.id = parseInt($(this).attr('data-id'));
                item.quantity = $(this).find('.quantity input').val();

                $(this).find('.variants .variant').each(function() {
                    var variantID = $(this).attr('data-id');
                    var value = $(this).val();
                    item.reference.options.variants[variantID] = value;
                });

                items.push(item);
            });

            //store
            $('form input[name="user_id"]').val($("#user-select").val());
            $('form input[name="items"]').val(JSON.stringify(items));
        });

        //setup chosen on product selector
        $("#add-modal select.product").chosen({width: '100%'});

        //set visible product options
        $("#add-modal select.product").change(function() {
            $("#add-modal .product-options .options.visible").hide();
            var id = $(this).val();
            $("#add-modal .product-options .options[data-product='" + id + "']").show().addClass('visible');
        });

        //add item
        $("#add-modal .modal-footer .add").click(function() {
            var id = $("#add-modal select.product").val();
            var name = $("#add-modal select option[data-id='" + id + "']").text();
            var options = {variants: {}};

            $("#add-modal .product-options .options.visible .variant").each(function() {
                var variantID = $(this).attr('data-id');
                var value = $(this).val();
                options.variants[variantID] = value;
            });

            var item = {id: id, name: name, options: options};

            var el = itemTemplate.clone();

            el.attr('data-id', item.id);
            el.find('.product .name').text(item.name);
            el.find('.quantity input').val(1);
            el.find('.variants').append($("#add-modal .product-options .options.visible .variant"));

            $("table tbody").append(el);
            el.show();

            $("#add-modal").gsModal('hide');
        });

        //remove item
        $("#items").on('click', 'tr.item button.remove', function() {
            $(this).parents('tr.item').remove();
        });
    </script>
@stop