@extends('master')

@section('scripts')
	<script>
		window.print();
	</script>
@stop

@section('body')
    <style>
        body {
            float: none;
            overflow: visible;
        }

        .container {
            float: none
        }

        .packlist {
            float: none
        }
        .packlist:not(:last-child) {
            page-break-after: always;
        }

        hr {
            background-color: #ddd
        }
    </style>

    <div class="container ninesixty" style="width: 780px;">
        @foreach($orders as $key => $order)
        <div class="row clearfix packlist" style="margin: 0; padding: 0;">
            <div class="row">
                <div class="col-xs-6 tight-left">
                    <img id="logo" src="{{asset('images/friluft.png')}}" alt="logo"/>
                </div>

                <div class="col-xs-6 tight-right text-right">
                    123Concept AS<br>
                    Postboks 27<br>
                    1751 Halden
                </div>
            </div>

            <hr class="small">

            <div class="col-xs-12 tight">
                <ul class="inline text-center end">
                    <li><strong>Kundenr: </strong> {{$order->user->id}}</li>
                    <li><strong>Ordrenr: </strong> {{$order->id}}</li>
                    <li><strong>E-Post: </strong> {{$order->user->email}}</li>
                    <li><strong>Telefon: </strong> {{$order->user->telefon}}</li>
                </ul>
            </div>

            <hr/>

            <div class="row tight">
                <div class="col-xs-6 tight-left">
                    <h5>Fakturaadresse</h5>
                    <address>
                        {{$order->billing_address['given_name']}} {{$order->billing_address['family_name']}}<br>
                        {{$order->billing_address['street_address']}}, {{$order->billing_address['postal_code']}}
                        {{$order->billing_address['city']}}, {{$order->billing_address['country']}}.
                    </address>
                </div>

                <div class="col-xs-6 tight-right">
                    <h5>Leveringsadresse</h5>
                    <address>
                        {{$order->shipping_address['given_name']}} {{$order->shipping_address['family_name']}}<br>
                        {{$order->shipping_address['street_address']}}, {{$order->shipping_address['postal_code']}}
                        {{$order->shipping_address['city']}}, {{$order->shipping_address['country']}}.
                    </address>
                </div>
            </div>

            <hr>

            <div class="row tight">
                <div class="col-xs-4 tight-left">
                    <p class="end">
                        Dette er ikke en faktura. Fakturaen vil bli sendt separat per e-post til {{$order->billing_address['email']}} eller via vanlig post dersom du har valt det.
                    </p>
                </div>

                <div class="col-xs-8 tight-right">
                    <h6>Spørsmål?</h6>
                    <ul class="end">
                        <li>Angående leverling og/eller varer, kontakt oss på <strong>kundeservice@123friluft.no</strong></li>
                        <li>Angående betaling, besøk <strong>klarna.no/support</strong></li>
                        <li>For bytte/retur gå til <strong>www.123friluft.no/bytte-og-retur</strong>.</li>
                    </ul>
                </div>
            </div>

            <hr>

            <div class="row tight">
                <div class="col-xs-12 tight">
                    <table class="bordered">
                        <thead>
                            <tr>
                                <th>Pakket</th>
                                <th>Produkt</th>
                                <th>Antall</th>
                                <th>Art. Nr</th>
                                <th>Lagerplass</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($order->items as $id => $item)
                            <?php
                                if ($item['type'] == 'shipping_fee') {
                                    continue;
                                }

                                # get product ID and model
                                $productID = $item['reference']['id'];
                                $product = Friluft\Product::find($productID);

                                $name = $product->name;
                                $manufacturer = ($product->manufacturer) ? $product->manufacturer->name : '';

                                $variantString = '';
								if ($product->hasVariants()) {
									# get the variants we ordered
									$variants = $item['reference']['options']['variants'];

									# build the string describing the variants
									if ($product->isCompound()) {
										foreach($product->getChildren() as $child) {
											foreach($child->variants as $variant) {
												$variantString .= $child->name .' ' .$variant->name .': ' .$variant->getValueName($variants[$variant->id]) .', ';
											}
										}
									}else {
										foreach($product->getVariants() as $variant) {
											$variantString .= $variant->name .': ' .$variant->getValueName($variants[$variant->id]) .', ';
										}
									}

									# get stock
									$stock = $product->getStock($item['reference']['options']);

									# (we want actual, physical stock so increment that)
									$stock++;
								}
                                $variantString = rtrim($variantString, ', ');
                            ?>
                            <tr class="item" data-id="{{$id}}">
                                <td>
                                    <div style="width: 24px; height: 24px; margin: 4px; border: 1px solid #333; border-radius: 2px;"></div>
                                </td>
                                <td>
                                    <strong>{{$manufacturer}}</strong>
                                    <span>{{$name .' (' .$variantString .')'}}</span>
                                </td>
                                <td>
                                    {{$item['quantity']}}
                                </td>
                                <td>{{$product->articlenumber}}</td>
                                <td>{{$product->barcode}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@stop