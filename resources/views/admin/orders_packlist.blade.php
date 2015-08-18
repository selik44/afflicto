@extends('master')

@section('scripts')
    @parent

	<script>
        //force each packlist to have a height of width * 1.414 for A4 pages.

        $(".packlist").each(function() {
            var width = $(this).width();
            var height = $(this).height();

            console.log('height is: ' + height);
            var a4 = width * 1.414;

            console.log('a4 height is: ' + a4);

            if (height > a4) {
                height = Math.ceil(height / a4) * a4;
            }else {
                height = a4;
            }

            console.log('result: ' + height);

            //$(this).css('height', height);
        });

		//window.print();
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
    </style>

    <div class="container ninesixty" style="width: 720px;">
        @foreach($orders as $key => $order)
        <div class="row clearfix packlist" style="margin: 0; padding: 0; page-break-after: always;">
            <div class="row">
                <div class="col-xs-6">
                    <img id="logo" src="{{asset('images/friluft.png')}}" alt="logo"/>
                </div>

                <div class="col-xs-6 text-right">
                    123Concept AS<br>
                    Johan Stangs Plass 2<br>
                    1767 Halden
                </div>
            </div>

            <hr/>

            <div class="row">
                <div class="col-xs-6">
                    <h5>Fakturaadresse</h5>
                    <address>
                        {{$order->billing_address['given_name']}} {{$order->billing_address['family_name']}}<br>
                        {{$order->billing_address['street_address']}}, {{$order->billing_address['postal_code']}}
                        {{$order->billing_address['city']}}, {{$order->billing_address['country']}}.
                    </address>
                </div>

                <div class="col-xs-6">
                    <h5>Leveringsadresse</h5>
                    <address>
                        {{$order->shipping_address['given_name']}} {{$order->shipping_address['family_name']}}<br>
                        {{$order->shipping_address['street_address']}}, {{$order->shipping_address['postal_code']}}
                        {{$order->shipping_address['city']}}, {{$order->shipping_address['country']}}.
                    </address>
                </div>

                <hr class="small"/>

                <div class="col-xs-12">
                    Kundenummer: {{$order->user->id}}<br>
                    Ordrenummer: {{$order->id}}<br>
                    E-Post: {{$order->user->email}}<br>
                    Telefon: {{$order->user->phone}}
                </div>
            </div>

            <hr/>

            <div class="row">
                <div class="col-xs-12">
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
                        <?php
                            $items = $order->items;

                            for($i = 0; $i < 5; $i++) {
                                foreach($items as $it) {
                                    $items[] = $it;
                                }
                            }

                        ?>
                        @foreach($items as $id => $item)
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
                                if (count($product->variants) > 0) {
                                    # get the variants we ordered
                                    $variants = $item['reference']['options']['variants'];

                                    # create the string describing the variants
                                    $stockID = [];
                                    foreach($variants as $variantID => $value) {
                                        $variantModel = Friluft\Variant::find($variantID);
                                        $variantString .= $variantModel->name .': ' .$value .', ';
                                    }
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