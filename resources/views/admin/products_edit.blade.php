@extends('admin.layout')

@section('title')
    {{$product->name}} @lang('admin.products') - @parent
@stop

@section('page')
    <h2>{{$product->name}}</h2>
    {!! $form->open
        ->action(route('admin.products.update', ['product' => $product]))
        ->method('PUT')
        ->id("product-form")
    !!}

    <div class="row">
        <div class="col-xs-6 col-l-7 col-xl-8">
            <h4>Tabs</h4>
            <ul class="nav tabs">
                <li class="current"><a href="#product-description">Description</a></li>
                <li><a href="#product-relations">Related Products</a></li>
                @if($product->tabs != null)
                    @foreach($product->tabs as $key => $tab)
                        <li><a href="#product-tab-{{$key}}">{{$tab['title']}}</a></li>
                    @endforeach
                @endif
            </ul>

            <div id="product-description">
                {!! $form->description->label("") !!}
            </div>
            <div id="product-relations" class="tab">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nostrum, temporibus.</p>
            </div>
            <div class="user-tabs">
                @if($product->tabs != null)
                    @foreach($product->tabs as $key => $tab)
                        <div class="tab" id="product-tab-{{$key}}">
                            <textarea name="product-tab-{{$key}}">{{$tab['body']}}</textarea>
                        </div>
                    @endforeach
                @endif
            </div>

            <hr/>

            <div id="add-variant-modal" class="modal fade center">
                <a href="#" style="font-size: 2.5rem" class="modal-dismiss" data-toggle-modal="#add-variant-modal"><i class="fa fa-close"></i></a>
                <div class="modal-header">Add Variant</div>
                <div class="modal-content">
                    <label for="variant-name">Name</label>
                    <input type="text" name="variant-name"/>

                    <label for="variant-values">Values</label>
                    <textarea style="width: 100%;" name="variant-values" rows="3"></textarea>
                    <span class="small muted">Separate values by commas <code>,</code>.</span>
                </div>
                <div class="modal-footer">
                    <button class="large success add-variant-add">Add</button>
                </div>
            </div>


            <div id="edit-variant-modal" class="modal fade center">
                <a href="#" style="font-size: 2.5rem" class="modal-dismiss" data-toggle-modal="#edit-variant-modal"><i class="fa fa-close"></i></a>
                <div class="modal-header"><h5 class="end">Edit Variant</h5></div>
                <div class="modal-content">

                    <label for="variant-values">Values</label>
                    <textarea style="width: 100%;" name="variant-values" rows="3"></textarea>
                    <span class="small muted">Separate values by commas <code>,</code>.</span>

                    <input type="hidden" name="variant-id"/>
                </div>
                <div class="modal-footer">
                    <button class="large success edit-variant-save">Save</button>
                </div>
            </div>


            <div class="product-variants row">
                <div class="module">
                    <div class="module-header clearfix">
                        <h6 class="title pull-left">Variants</h6>
                        <button class="pull-right large" data-toggle-modal="#add-variant-modal"><i class="fa fa-plus"></i> Add</button>
                    </div>

                    <div class="module-content" style="padding: 0">
                        <table class="bordered">
                            <thead>
                                <tr>
                                    <th><strong>Name</strong></th>
                                    <th><strong>Values</strong> <div class="pull-right"><strong>Stock</strong></div></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($product->variants as $variant)
                                <tr class="variant" data-id="{{$variant->id}}">
                                    <td class="name">{{$variant->name}}</td>
                                    <td class="values">
                                        <ul class="flat">
                                            @foreach($variant->data['values'] as $value)
                                                <li class="value clearfix">
                                                    <span class="value-name pull-left">{{$value['name']}}</span>
                                                    <input type="number" data-value="{{$value['name']}}" class="value-stock pull-right" style="width: 60px" value="{{$value['stock']}}"/>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="actions">
                                        <div class="button-group pull-right">
                                            <button class="small variant-edit"><i class="fa fa-pencil"></i></button>
                                            <button class="small variant-delete error"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr/>

            <div class="product-images row">
                <div class="module">
                    <div class="module-header clearfix">
                        <h6 class="title">Images</h6>
                    </div>

                    <div class="module-content dropzone" style="padding: 0; min-height: 80px;" id="product-images-list">

                    </div>
                </div>
            </div>

        </div>

        <div class="product-data-view col-xs-6 col-l-5 col-xl-4">
            <h4>Product Data</h4>
            <div class="row">
                <div class="col-xs-6 tight-left">
                    {!! $form->name !!}
                </div>
                <div class="col-xs-6 tight-right">
                    {!! $form->slug !!}
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6 tight-left">
                    {!! $form->articlenumber !!}
                </div>
                <div class="col-xs-6 tight-right">
                    {!! $form->barcode !!}
                </div>
            </div>

            <hr/>

            <div class="row">
                <div class="col-xs-6 tight-left">
                    {!! $form->inprice !!}
                </div>

                <div class="col-xs-6 tight-right">
                    {!! $form->vatgroup !!}
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6 tight-left">
                    {!! $form->price !!}
                </div>

                <div class="col-xs-6 tight-right">
                    <label for="profit">Profit (Price excluding MVA and In price)</label>
                    <input type="text" name="profit" id="profit">
                </div>
            </div>

            <hr/>

            <div class="row">
                <div class="col-xs-6 tight-left">
                    {!! $form->stock !!}
                </div>
                <div class="col-xs-6 tight-right">
                    {!! $form->weight !!}
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6 tight-left">
                    {!! $form->manufacturer !!}
                </div>
                <div class="col-xs-6 tight-right">
                    {!! $form->categories !!}
                </div>
            </div>

            <hr/>

            {!! $form->summary !!}
        </div>
    </div>


    <div class="footer-height-fix"></div>
    <footer id="footer">
        <div class="inner">
            <input type="submit" name="save" value="Save" class="large success end">
        </div>
    </footer>
    {!!Former::close()!!}
@stop



@section('scripts')
    @parent
    <script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
    <script type="text/javascript">
        var productID = "{{$product->id}}";
        var form = $("form");

        //initialize dropzone for images
        $("#product-images-list").dropzone({
            url: Friluft.URL + '/admin/api/products/' + productID + '/images',

            init: function() {
                this.on('sending', function(file, xhr, formData) {
                    formData.append('_token', Friluft.token);
                });
            }
        });

        //initialize chosen
        form.find('[name="categories[]"]').chosen().next().removeAttr('style').css('width', '100%');
        form.find('[name="vatgroup"]').chosen().next().removeAttr('style').css('width', '100%');
        form.find('[name="manufacturer"]').chosen().next().removeAttr('style').css('width', '100%');

        //initialize add variant modal
        $("#add-variant-modal").gsModal();
        $("#add-variant-modal .add-variant-add").click(function() {
            var self = $(this);
            self.attr('disabled', 'disabled');

            var modal = $("#add-variant-modal");
            var name = modal.find('[name="variant-name"]').val();
            var values = modal.find('[name="variant-values"]').val();

            var payload = {_token: Friluft.token, name: name, values: values};
            $.post(Friluft.URL + '/admin/api/products/' + productID + '/variants', payload, function(response) {
                self.removeAttr('disabled');
                $("#add-variant-modal").gsModal('hide');
                $("#product-form").trigger('submit');
            });
        });

        //edit variant
        var editModal = $("#edit-variant-modal");
        $(".product-variants .variant .variant-edit").click(function(e) {
            e.preventDefault();

            //get variant data
            var view = $(this).parents('.variant').first();
            var values = view.find('.values').text();
            var id = view.attr('data-id');

            //set form data
            editModal.find('[name="variant-values"]').val(values);
            editModal.find('[name="variant-id"]').val(id);

            // show modal
            editModal.gsModal('show');
        });

        //save variant
        editModal.find('.modal-footer .edit-variant-save').click(function(e) {
            editModal.gsModal('hide');
            var name = editModal.find('[name="variant-name"]').val();
            var values = editModal.find('[name="variant-values"]').val();
            var id = editModal.find('[name="variant-id"]').val();

            var payload = {
                _method: 'PUT',
                _token: Friluft.token,
                values: values
            };

            $.post(Friluft.URL + '/admin/api/products/' + productID + '/variants/' + id, payload, function(response) {
                console.log('saved variant, response:');
                console.log(response);
                $("#product-form").trigger('submit');
            });
        });

        //update variant stock
        //api/products/{product}/variants/{variant}/{value}/{stock}
        $(".product-variants .variant .values .value input.value-stock").change(function() {
            var value = $(this).attr('data-value');
            var stock = $(this).val();
            var variantID = $(this).parents('.variant').first().attr('data-id');

            var payload = {
                _method: 'PUT',
                _token: Friluft.token,
                value: value,
                stock: stock,
            };

            $.post(Friluft.URL + '/admin/api/products/' + productID + '/variants/' + variantID + '/setstock', payload, function(response) {
                console.log('updated stock, response:');
                console.log(response);
            });
        });

        //delete variant
        $(".product-variants .variant .variant-delete").click(function(e) {
            e.preventDefault();

            var id = $(this).parents('.variant').first().attr('data-id');

            console.log('deleting variant by id: ' + id);

            $.post(Friluft.URL + '/admin/api/products/' + productID + '/variants/' + id, {_token: Friluft.token, _method: 'DELETE'}, function(response) {
                console.log('deleted variant, response:');
                console.log(response);
                $("#product-form").trigger('submit');
            });
        });

        //auto-price
        var profit = form.find('[name="profit"]');
        var price = form.find('[name="price"]');
        var vatgroup = form.find('[name="vatgroup"]');
        var inprice = form.find('[name="inprice"]');

        function getTaxPercent() {
            var taxPercent = vatgroup.siblings('.chosen-container').find('.chosen-single span').text();
            if (/[0-9]+%/.test(taxPercent) == false) return 1;
            taxPercent = parseInt(taxPercent.substr(0, taxPercent.length -1));
            return 1 + (taxPercent / 100);
        }

        function getProfit() {
            return parseFloat(profit.val());
        }

        function getInPrice() {
            return parseFloat(inprice.val());
        }

        function getPrice() {
            return parseFloat(price.val());
        }

        profit.bind('keyup', function(e) {
            price.val((getProfit() + getInPrice()) * getTaxPercent());
        });

        price.bind('keyup', function(e) {
            var calculatedProfit = (getPrice() / getTaxPercent()) - getInPrice();
            profit.val(calculatedProfit);
        });

        //set profit value
        profit.val((getPrice() / getTaxPercent()) - getInPrice());

        //autoslug the name
        form.find('[name="slug"]').autoSlug({other: '[name="name"]'});

        //init ckeditor
        CKEDITOR.stylesSet.add('friluft', [
            //blocks
            {name: 'Heading 3', element: 'h3'},
            {name: 'Heading 4', element: 'h4'},
            {name: 'Heading 5', element: 'h5'},
            {name: 'Heading 6', element: 'h6'},
            {name: 'Lead Paragraph', element: 'p', attributes: {'class': 'lead'}},
            {name: 'Paragraph', element: 'p'},
            {name: 'Small Paragraph', element: 'p', attributes: {'class': 'small'}},
            {name: 'Muted Paragraph', element: 'p', attributes: {'class': 'muted'}},
            {name: 'Small & Muted Paragraph', element: 'p', attributes: {'class': 'muted small'}},
            {name: 'Blockquote', element: 'blockquote'},
        ]);

        CKEDITOR.replace("description", {
            language: '{{\App::getLocale()}}',
            contentsCss: '{{asset('css/friluft.css')}}',
            stylesSet: 'friluft',
        });
    </script>
@stop