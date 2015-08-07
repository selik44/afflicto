@extends('admin.layout')

@section('title')
    {{$product->name}} @lang('admin.products') - @parent
@stop

@section('page')
    {!! $form->open
        ->action(route('admin.products.update', ['product' => $product]))
        ->method('PUT')
        ->id("product-form")
    !!}

    <style>
	    .tab {background: white;}
    </style>

    <header class="row product-title">
	    <div class="col-xs-12 tight">
		    {!! $form->name->class('large')->label(null) !!}
	    </div>
    </header>

    <hr/>

    <div class="row">
        <div class="col-l-6 col-xl-8">

	        <div class="product-tabs-row module" style="overflow: visible;">
		        <header class="module-header clearfix">
			        <h6 class="title pull-left">Tabs</h6>
			        <a class="button pull-right large add-tab"><i class="fa fa-plus"></i> Add</a>
		        </header>
	            <ul class="nav tabs">
	                <li class="current"><a href="#product-description">Description</a></li>
	                <li><a href="#product-relations">Related Products</a></li>
                    @foreach($product->producttabs as $tab)
                        <li><a href="#tab-{{$tab->id}}">{{$tab->title}}</a></li>
                    @endforeach
	            </ul>

	            <div id="product-description">
	                {!! $form->description->label("") !!}
	            </div>

	            <div id="product-relations" class="tab clearfix">
		            <ul class="flat relations">
			            @foreach($product->relations as $related)
				            <li class="relation" data-id="{{$related->id}}">
					            <span class="name">{{($related->manufacturer) ? $related->manufacturer->name : ''}} {{$related->name}} <a class="unrelate" href="#"><i class="fa fa-trash color-error"></i></a></span>
				            </li>
			            @endforeach
		            </ul>

		            <hr>

		            <div class="row">
			            <div class="col-xs-10 tight-left">
				            <select name="relations" id="relations-select">
					            @foreach(\Friluft\Product::all() as $p)
									{{-- we don't want to relate A with A --}}
					                @unless($p->id == $product->id)
										<option value="{{$p->id}}">{{($p->manufacturer) ? $p->manufacturer->name : ''}}: {{$p->name}}</option>
						            @endunless
								@endforeach
				            </select>
			            </div>

			            <div class="col-xs-2 tight-right">
				            <button style="width: 100%;" class="primary add-relation"><i class="fa fa-plus"></i> Relate</button>
			            </div>
		            </div>
	            </div>

	            <div id="product-tabs">
                    @foreach($product->producttabs as $tab)
                        <div class="tab" id="tab-{{$tab->id}}">
                            <input type="text" name="tab-{{$tab->id}}-title" value="{{$tab->title}}">
                            <input type="hidden" name="tab-{{$tab->id}}-id" value="{{$tab->id}}">
                            <textarea name="tab-{{$tab->id}}">{{$tab->body}}</textarea>
                            <br>
                            <button class="error remove"><i class="fa fa-trash"></i> Delete Tab</button>
                        </div>
                    @endforeach
	            </div>
	        </div>

            <hr/>

            <div class="product-variants row">
                <div class="module">
                    <div class="module-header clearfix">
                        <h6 class="title">Variants</h6>
                    </div>

                    <div class="module-content" style="padding: 0">
                        <table class="bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                    $stock = $product->variants_stock;

                                    if ( ! $stock) {
                                        $stock = [];
                                    }


                                    if ( ! $product->variants->isEmpty()) {
                                        $rootVariant = $product->variants[0];
                                        if (count($product->variants) > 1) {
                                            foreach($rootVariant->data['values'] as $rootValue) {
                                                foreach($product->variants as $variant) {
                                                    if ($rootVariant == $variant) continue;

                                                    foreach($variant['data']['values'] as $value) {
                                                        $stockID = $rootValue['id'] .'_' .$value['id'];
                                                        $s = 0;
                                                        if (isset($stock[$stockID])) {
                                                            $s = $stock[$stockID];
                                                        }

                                                        echo '<tr>';
                                                        echo '<td>' .$rootValue['name'] .' ' .$value['name'] .'</td>';
                                                        echo '<td><input type="text" name="variant-' .$stockID .'" value="' .$s .'"></td>';
                                                        echo '<tr>';
                                                    }
                                                }
                                            }
                                        }else {
                                            foreach($rootVariant->data['values'] as $value) {

                                                $stockID = $value['id'];

                                                $s = 0;
                                                if (isset($stock[$stockID])) {
                                                    $s = $stock[$stockID];
                                                }

                                                echo '<tr>';
                                                echo '<td>' .$value['name'] .'</td>';
                                                echo '<td><input type="text" name="variant-' .$value['id'] .'" value="' .$s .'"></td>';
                                                echo '</tr>';
                                            }
                                        }
                                    }
                                ?>
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

                    <div class="module-content dropzone row" style="padding: 0; min-height: 80px;" id="product-images-list">
                        <div class="dz-preview dz-file-preview preview-template clearfix">
                            <div class="col-xs-4 preview-image">
                                <div class="handle">
                                    <img data-dz-thumbnail>
                                </div>
                            </div>
                            <div class="col-xs-6 preview-info">
                                <div class="dz-details">
                                    <div class="dz-filename"><h6 data-dz-name></h6></div>
                                    <div class="dz-size" data-dz-size></div>
                                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                                    <div class="dz-success-mark"><i class="fa fa-check color-success"></i></div>
                                    <div class="dz-error-mark"><i class="fa fa-close color-error"></i></div>
                                    <div class="dz-error-message alert alert-error"><h6>Error</h6><p><span data-dz-errormessage></span></p></div>
                                </div>
                                <footer class="footer">
                                    <div class="button-group">
                                        <button class="small error delete"><i class="fa fa-trash"></i> Delete</button>
                                    </div>
                                </footer>
                            </div>
                        </div>

                        @foreach($product->images()->orderBy('order', 'asc')->get() as $image)
                            <div data-id="{{$image->id}}" class="dz-sortable dz-preview dz-file-preview dz-success dz-complete preview-template">
                                <div class="col-xs-4 preview-image">
                                    <div class="handle">
                                        <img data-dz-thumbnail src="{{asset('images/products/' .$image->name)}}">
                                    </div>
                                </div>
                                <div class="col-xs-6 preview-info">
                                    <div class="dz-details">
                                        <div class="dz-filename"><h6 data-dz-name>{{$image->name}}</h6></div>
                                        <div class="dz-size" data-dz-size>{{filesize(public_path('images/products/' .$image->name))}}</div>
                                    </div>
                                    <footer class="footer">
                                        <div class="button-group image-actions">
                                            <button class="small error delete"><i class="fa fa-trash"></i> Delete</button>
                                        </div>
                                    </footer>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <div class="product-data-view col-l-6 col-xl-4">
            <h4>Product Data</h4>
            <div class="row">
	            <div class="col-xs-4 tight-left">
		            {!! $form->enabled->class('large') !!}
	            </div>
	            <div class="col-xs-8 tight-right">
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
                    {!! $form->price->help('Inkl MVA: <span class="value">255</span>,-') !!}
                </div>

                <div class="col-xs-6 tight-right">
                    <label for="profit">Profit (Price excluding MVA and In price)</label>
                    <input type="text" name="profit" id="profit">
                </div>
            </div>

            <hr/>

            <div class="row">
                <div class="col-xs-6 tight-left">
                    @if(count($product->variants) <= 0)
                        {!! $form->stock !!}
                    @endunless
                </div>
                <div class="col-xs-6 tight-right">
                    {!! $form->weight !!}
                </div>
            </div>

            <div class="row">
                {!! $form->manufacturer !!}
                {!! $form->categories !!}
	            {!! $form->tags !!}
                {!! $form->variants !!}
            </div>

            <hr/>

	        <div class="row">
                {!! $form->summary !!}
	        </div>
        </div>
    </div>


    <div class="footer-height-fix"></div>
    <footer id="footer">
        <div class="inner">
            <input type="submit" name="save" value="Save" class="large success end">
        </div>
    </footer>
    {!!Former::close()!!}

    <div id="add-tab-modal" class="modal center fade">
        <button class="modal-dismiss" data-toggle-modal="#add-tab-modal"><i class="fa fa-close"></i></button>
        <div class="modal-header">
            <h4 class="end">New Tab</h4>
        </div>

        <div class="modal-content">
            <input type="text" name="title" value="New Tab">

            <br>

            <div class="button-group">
                <button class="primary large add">Add</button>
                <button class="large cancel">Cancel</button>
            </div>
        </div>
    </div>
@stop



@section('scripts')
    @parent
    <script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
    <script type="text/javascript">
        var productID = "{{$product->id}}";
        var form = $("form");

        //init relations
        var relations = $("#product-relations");

        //init chosen for relations select
        relations.find('select[name="relations"]').chosen().next().removeAttr('style').css('width', '100%');

        //add relation
        $(document).on('click', '#product-relations button.add-relation', function(e) {
	        e.preventDefault();

	        var related = relations.find('select[name="relations"]').val();
	        var relatedName = relations.find('select[name="relations"] option[value="' + related + '"]').text();

	        console.log('related name: ' + relatedName);

	        var payload = {_token: Friluft.token, _method: 'PUT'};
	        $.post(Friluft.URL + '/admin/products/' + productID + '/relate/' + related, payload, function(response) {
		        console.log('added relation, response:');
		        console.log(response);

		        if (response == 'OK') {
			        //add relation to UI
			        relations.find('ul.relations').append('<li class="relation" data-id="' + related + '"><span class="name">' + relatedName + ' <a href="#" class="unrelate"><i class="color-error fa fa-trash"></i></a></span></li>');
		        }
	        });
        });

        //remove relation
        $(document).on('click', '#product-relations .relations .relation .unrelate', function(e) {
	        e.preventDefault();

	        var element = $(this).parents('.relation');

	        var related = $(this).parents('.relation').attr('data-id');

	        var payload = {_token: Friluft.token, _method: 'PUT'}
	        $.post(Friluft.URL + '/admin/products/' + productID + '/unrelate/' + related, payload, function(response) {
		        console.log('removed relation' + related + ', response:');
		        console.log(response);

		        if (response == 'OK') element.slideUp(function() {$(this).remove()});
	        });
        });


        //---------------- product tabs ------------------//
        var tabs = $("#product-tabs");
        var addTabModal = $("#add-tab-modal").gsModal();

        //show add tab modal
        $(".product-tabs-row .module-header a.add-tab").click(function() {
            addTabModal.find('input[name="title"]').val("New Tab");
            addTabModal.gsModal('show');
        });

        //cancel add
        addTabModal.find('button.cancel').click(function() {
            addTabModal.gsModal('hide');
        });

        //add
        addTabModal.find('button.add').click(function() {
            addTabModal.gsModal('hide');

            var title = addTabModal.find('input[name="title"]').val();

            var id = tabs.find('.tab').length + 1;

            //create the tab itself
            var tab = $("<div style='display: none;' class='tab' id='tab-" + id + "'><input type='text' class='title' name='tab-" + id + "-title' value='" + title + "'><textarea class='tab-content' name='tab-" + id + "-content'></textarea><br><button class='error remove'><i class='fa fa-trash'></i> Delete Tab</button></div>");
            tabs.append(tab);

            //create the tab link
            var link = $("<li><a href='#tab-" + id + "'>" + title + "</a></li>");
            $(".product-tabs-row ul.nav.tabs").append(link);
        });

        //remove tab
        tabs.on('click', '.tab > button.remove', function(e) {
            e.preventDefault();
            var tab = $(this).parent('.tab');
            var id = tab.attr('id');
            var link = $(".product-tabs-row ul.nav.tabs li a[href='#" + id + "']");

            var dbId = $('input[name="' + id + '-id"]').val();

            console.log('db id: ' + dbId);

            if (dbId == null) {
                return false;
            }

            var payload = {
                _method: 'DELETE',
                _token: Friluft.token,
            };

            $.post(Friluft.URL + '/admin/products/tabs/' + dbId, payload, function(response) {
                console.log('tab deleted.');
                tab.remove();
                link.remove();
            });
        });

        var previewNode = document.querySelector(".preview-template");
        previewNode.id = "";
        var previewTemplate = previewNode.parentNode.innerHTML;
        previewNode.parentNode.removeChild(previewNode);

        $("#product-images-list").dropzone({
            url: Friluft.URL + '/admin/api/products/' + productID + '/images',
            previewTemplate: previewTemplate,

            init: function() {
                //move the .dz-message to the start
                var el = $(this.element);
                el.find('.dz-message').detach().prependTo(el);

                this.on('sending', function(file, xhr, formData) {
                    formData.append('_token', Friluft.token);
                });
            }
        });

        //initialize sortable
        $("#product-images-list").sortable({
            items: '.dz-sortable',
            handle: '.handle',
            forcePlaceholderSize: true,
            placeholder: '<div class="placeholder"></div>'
        });

        //persist changes of image ordering.
        $("#product-images-list").on('sortupdate', function() {
            console.log('sortupdate...');
            var tree = [];
            var order = 0;

            console.log('tree');
            console.log(tree);

            $("#product-images-list .dz-sortable").each(function() {
                var id = $(this).attr('data-id');
                console.log(id + ' => ' + order);
                tree[id] = {id: id, order: order};
                order++;
            });

            console.log('tree:');
            //console.log(tree);

            var payload = {
                _method: 'PUT',
                _token: Friluft.token,
                order: JSON.stringify(tree),
            };

            $.post(Friluft.URL + '/admin/api/products/' + productID + '/images/order', payload, function(response) {
                console.log('updated image order, response:');
                //console.log(response);
            });
        });

        //delete images
        $("#product-images-list .image-actions .delete").click(function(e) {
            var preview = $(this).parents('.dz-sortable').first();
            e.preventDefault();
            var payload = {
                _method: 'DELETE',
                _token: Friluft.token,
                id: preview.attr('data-id')
            };

            $.post(Friluft.URL + '/admin/api/products/' + productID + '/images', payload, function(response) {
                preview.slideUp(function() {
                    $(this).remove();
                });
            });
        });

        //initialize chosen
        form.find('[name="categories[]"]').chosen({width: '100%'});
        form.find('[name="vatgroup"]').chosen({width: '100%'});
        form.find('[name="manufacturer"]').chosen({width: '100%'});
        form.find('[name="tags[]"]').chosen({width: '100%'});
        form.find('[name="variants[]"]').chosen({width: '100%'});

        /*
        $.post(Friluft.URL + '/admin/api/products/' + productID + '/variants/' + variantID + '/setstock', payload, function(response) {
            console.log('updated stock, response:');
            console.log(response);
        });
        */

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
        var priceHelp = price.parent('.controls').find('.muted .value');
        var vatgroup = form.find('[name="vatgroup"]');
        var inprice = form.find('[name="inprice"]');

        function getTaxPercent() {
            var taxPercent = vatgroup.siblings('.chosen-container').find('.chosen-single span').text();
            if (/[0-9]+%/.test(taxPercent) == false) return 1;
            taxPercent = parseInt(taxPercent.substr(0, taxPercent.length -1));
            return 1 + (taxPercent / 100);
        }

        function getProfit() {
            return parseInt(profit.val());
        }

        function getInPrice() {
            return parseInt(inprice.val());
        }

        function getPrice() {
            return parseInt(price.val());
        }

        function calculateProfit() {
	        var profit = getPrice() - getInPrice();

            profit -= ((profit * getTaxPercent()) - profit);

            return Math.round(profit);
        }

        function updatePrice() {
            var priceValue = (getProfit() + getInPrice());
            console.log('updating price to ' + priceValue);
            price.val(priceValue);
            console.log('inc MVA: ' + (priceValue * getTaxPercent()));
            priceHelp.html(Math.round(priceValue * getTaxPercent()));
        }

        profit.bind('keyup', function(e) {
            updatePrice();
        });

        price.bind('keyup', function(e) {
            profit.val(getPrice() - getInPrice());
            priceHelp.html(getPrice() * getTaxPercent());
        });

        inprice.bind('keyup', function(e) {
	        updatePrice();
        });

        vatgroup.bind('change', function(e) {
	        updatePrice();
        });

        //set profit value
        profit.val(getPrice() - getInPrice());

        priceHelp.html(Math.round(getPrice() * getTaxPercent()));

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