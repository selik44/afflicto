@extends('admin.layout')

@section('title')
    {{$product->name}} @lang('admin.products') - @parent
@stop

@section('page')
    <h2>{{$product->name}}</h2>
    {!! $form->open
        ->action(route('admin.products.update', ['product' => $product]))
        ->method('PUT')
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
                    <textarea style="width: 100%" name="variant-values" rows="3"></textarea>
                    <span class="small muted">Separate values by commas <code>,</code>.</span>
                </div>
                <div class="modal-footer">
                    <button class="large success add-variant-add">Add</button>
                </div>
            </div>


            <div id="edit-variant-modal" class="modal fade center">
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
                    <button class="large success edit-variant-save">Save</button>
                </div>
            </div>


            <div class="product-variants row">
                <div class="module">
                    <div class="module-header clearfix">
                        <h6 class="title pull-left">Variants</h6>
                        <button class="pull-right large" data-toggle-modal="#add-variant-modal">Add</button>
                    </div>

                    <div class="module-content">
                        <table>
                            <thead>
                                <tr>
                                    <th><strong>Name</strong></th>
                                    <th colspan="2"><strong>Values</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($product->variants as $variant)
                                <tr class="variant">
                                    <td>{{$variant['name']}}</td>
                                    <td>{{implode(',', $variant['values'])}}</td>
                                    <td class="actions">
                                        <div class="button-group pull-right">
                                            <button class="tiny variant-edit">Edit</button>
                                            <button class="tiny variant-delete error">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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
                console.log(response);
                self.removeAttr('disabled');
            });
        });

        //edit variant
        var editModal = $("#edit-variant-modal");
        $(".product-variants .variant .variant-edit").click(function(e) {
            e.preventDefault();
            editModal.gsModal('show');
        });

        //save variant
        editModal.find('.modal-footer .save').click(function(e) {
            editModal.gsModal('hide');
            var
        });

        //delete variant
        $(".product-variants .variant .variant-delete").click(function(e) {
            e.preventDefault();
            console.log('delete variant');
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