@extends('front.layout')

@section('title')
    Checkout - @parent
@stop

@section('breadcrumbs', Breadcrumbs::render('store.checkout'))

@section('article')
    <div class="row tight checkout-view">
        <div class="visible-l-up col-l-3 tight-left checkout-products">
            <?php
            $checkoutTag = Friluft\Tag::whereType('checkout')->first();
            ?>

            <div class="grid paper">
                @foreach($checkoutTag->products as $product)
                    @include('front.partial.products-block', ['product' => $product, 'withBuyButton' => true])
                @endforeach
            </div>
        </div>

        <div class="col-m-8 col-xl-7 col-l-6 cart">
            <div class="paper">
                @include('front.partial.cart-table', ['items' => $items, 'total' => $total, 'withShipping' => true, 'withTotal' => false])

                <hr>

                <label class="checkbox-container end" style="float: left; width: 100%;" for="subscribe_to_newsletter">@lang('validation.attributes.subscribe_to_newsletter')
                    <div class="checkbox">
                        {!! Former::checkbox('subscribe_to_newsletter')->label(null) !!}
                        <span></span>
                    </div>
                </label>

                {!! $snippet !!}
            </div>
        </div>

        <div class="col-xs-3 col-m-4 col-l-3 col-xl-2 tight-right aside">
            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-fighter-jet"></i> &nbsp;&nbsp; Rask Levering</h5>
                    </div>
                    <div class="module-content">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur, quae?</p>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-check-square-o"></i> &nbsp;&nbsp; Trygg Betaling</h5>
                    </div>
                    <div class="module-content">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing.</p>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-calendar"></i> &nbsp;&nbsp; 30 Dagers Åpent Kjøp</h5>
                    </div>
                    <div class="module-content">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A eaque earum neque sapiente sit voluptatum?</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('scripts')
    @parent

    <script type="text/javascript">
        (function() {
            $(document).ready(function() {
                $("#klarna-checkout-container").css('overflow-x', 'visible');
            });

            //set subscribe to newsletter
            $("#subscribe_to_newsletter").change(function() {
                console.log('Subscribe changed!');
                var payload = {
                    _token: Friluft.token,
                };
                var val = 0;
                if ($(this).is(':checked')) val = 1;
                $.post(Friluft.URL + '/cart/setsubscribe/' + val, payload, function(response) {
                    console.log(response);
                });
            });

            // initialize isotope
            imagesLoaded(document.querySelector('.products-grid'), function() {
                $(".products-grid").isotope({
                    itemSelector: '.product',
                    layoutMode: 'fitRows',
                });
            });
        })();
    </script>
@stop