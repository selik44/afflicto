@extends('front.layout')

@section('title')
    Checkout - @parent
@stop

@section('breadcrumbs', Breadcrumbs::render('store.checkout'))

@section('aside')
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
@stop

@section('article')
    <div class="paper row" style="padding: 2rem;">
        @include('front.partial.cart-table', ['items' => $items, 'total' => $total, 'withShipping' => true])
    </div>

    {!! $snippet !!}
@stop


@section('scripts')
    @parent

    <script type="text/javascript">
        $(document).ready(function() {
            $("#klarna-checkout-container").css('overflow-x', 'visible');
        });
    </script>
@stop