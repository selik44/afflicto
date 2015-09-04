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

				<div class="coupon-container">
					<div class="code">
						<label for="coupon_code">Rabbatkode
							<div class="input-append" style="width: 100%;">
								<?php
									$code = isset($coupon) ? $coupon->code : '';
								?>
								<input type="text" name="coupon_code" value="{{$code}}" placeholder="Rabattkode...">
								<div class="appended">
									<button class="update primary" style="flex: auto">Oppdater</button>
								</div>
							</div>
						</label>
					</div>



					<div class="info">
						@if($coupon)

							<i class="fa fa-check color-success"></i> <span class="name">{{ $coupon->name }}</span>
						@endif
					</div>

					<div class="alert warning" style="display: none">
						<h6>Oops!</h6>
						<p></p>
					</div>
				</div>

				<hr>

                <label class="checkbox-container end" style="float: left; width: 100%;" for="subscribe_to_newsletter">@lang('validation.attributes.subscribe_to_newsletter')
                    <div class="checkbox">
                        {!! Former::checkbox('subscribe_to_newsletter')->check()->label(null) !!}
                        <span></span>
                    </div>
                </label>

                {!! $snippet !!}
            </div>
        </div>

        <div class="visible-m-up col-m-4 col-l-3 col-xl-2 tight-right aside">
            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-fighter-jet"></i> &nbsp;&nbsp; Rask Levering</h5>
                    </div>
                    <div class="module-content">
                        {!! Friluft\Setting::whereMachine('checkout_1_content')->first()->value !!}
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-check-square-o"></i> &nbsp;&nbsp; Trygg Betaling</h5>
                    </div>
                    <div class="module-content">
                        {!! Friluft\Setting::whereMachine('checkout_2_content')->first()->value !!}
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="module">
                    <div class="module-header">
                        <h5><i class="fa fa-calendar"></i> &nbsp;&nbsp; 30 Dagers Åpent Kjøp</h5>
                    </div>
                    <div class="module-content">
                        {!! Friluft\Setting::whereMachine('checkout_3_content')->first()->value !!}
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

            //hide the buy modal
            $("#buy-modal").remove();

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
					console.log('Set newsletter, response: ');
                    console.log(response);
                });
            });

			//set subscribe to newsletter default
			var val = 0;
			if ($('#subscribe_to_newsletter').is(':checked')) {
				val = 1;
			}
			$.post(Friluft.URL + '/cart/setsubscribe/' + val, {_token: Friluft.token}, function(response) {
				console.log('Set newsletter, response: ');
				console.log(response);
			});

			// coupon code handling
			$(".coupon-container button.update").click(function() {
				var code = $(".coupon-container input").val();
				if (code.length <= 0) return false;

				console.log('applying coupon code ' + code);
				klarnaSuspend();

				$.post(Friluft.URL + '/api/cart/coupon/' + code, {_method: 'PUT', _token: Friluft.token}, function(response) {
					console.log('response: ');
					console.log(response);

					if (response == 'invalid code') {
						//reset info and show alert
						$(".coupon-container .info .name").text('').parent().hide();
						$(".coupon-container .alert p").text("Beklager, koden er ikke riktig!").parent().show();
					}else {
						//show info and hide alert
						$(".coupon-container .info .name").text(response.name).parent().show();
						$(".coupon-container .alert").hide();
					}

					klarnaResume();
				});
			});

            // initialize isotope
			if ($('.products-grid').length > 0) {
				imagesLoaded(document.querySelector('.products-grid'), function() {
					$(".products-grid").isotope({
						itemSelector: '.product',
						layoutMode: 'fitRows',
					});
				});
			}
        })();
    </script>
@stop