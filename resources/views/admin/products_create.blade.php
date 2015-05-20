@extends('admin.layout')

@section('title')
    @lang('admin.new') @lang('admin.product') - @parent
@stop

@section('page')
    <h2>@lang('admin.new') @lang('admin.product')</h2>

    {!! $form->open !!}
    <div class="row">
        <div class="col-xs-3 col-m-2 col-l-1">
            {!! $form->enabled !!}
        </div>
        <div class="col-xs-5 col-m-5 col-l-2">
            {!! $form->articlenumber !!}
        </div>
        <div class="col-xs-4 col-m-5 col-l-2">
            {!! $form->barcode !!}
        </div>

        <hr class="visible-xs"/>

        <div class="col-xs-7 col-l-4">
            {!! $form->name !!}
        </div>
        <div class="col-xs-5 col-l-3">
            {!! $form->slug !!}
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-xs-4">
            {!! $form->inprice !!}
        </div>

        <div class="col-xs-4">
            <label for="profit">Profit (Price excluding MVA and In price)</label>
            <input type="text" name="profit" id="profit">
        </div>

        <div class="col-xs-2">
            {!! $form->vatgroup !!}
        </div>

        <div class="col-xs-2">
            {!! $form->price !!}
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-xs-6 col-m-2 col-l-2">
            {!! $form->stock !!}
        </div>
        <div class="col-xs-6 col-m-2 col-l-2">
            {!! $form->weight !!}
        </div>
        <div class="col-xs-5 col-m-4 col-l-3">
            {!! $form->manufacturer !!}
        </div>
        <div class="col-xs-7 col-m-4 col-l-5">
            {!! $form->categories !!}
        </div>
    </div>

    <hr/>

    <div class="row">
        {!! $form->summary !!}
    </div>

    <div class="footer-height-fix"></div>

    <footer id="footer">
        <div class="inner">
            <input type="submit" name="create" value="Create" class="large primary end">
        </div>
    </footer>
	{!!Former::close()!!}
@stop

@section('scripts')
	@parent
    <script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
	<script type="text/javascript">
		var form = $("form");

        //initialize chosen
		form.find('[name="categories[]"]').chosen().next().removeAttr('style').css('width', '100%');
        form.find('[name="vatgroup"]').chosen().next().removeAttr('style').css('width', '100%');
        form.find('[name="manufacturer"]').chosen().next().removeAttr('style').css('width', '100%');

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
        };

        function getProfit() {
            return parseFloat(profit.val());
        };

        function getInPrice() {
            return parseFloat(inprice.val());
        }

        function getPrice() {
            return parseFloat(price.val());
        };

        profit.bind('keyup', function(e) {
            price.val((getProfit() + getInPrice()) * getTaxPercent());
        });

        price.bind('keyup', function(e) {
            var calculatedProfit = (getPrice() / getTaxPercent()) - getInPrice();
            profit.val(calculatedProfit);
        });

        vatgroup.bind('change', function() {
            price.val((getProfit() + getInPrice()) * getTaxPercent());
            profit.val((getPrice() / getTaxPercent()) - getInPrice());
        });

        //autoslug the name
		form.find('[name="slug"]').autoSlug({other: '[name="name"]'});
	</script>
@stop