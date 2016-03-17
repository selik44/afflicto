@foreach($category->products as $product)
    <li class="product module clearfix" data-id="{{$product->id}}">
        <header class="row header module-header">
            <a class="handle pull-left" href="#"><i class="fa fa-bars"></i></a>
            <h5 class="pull-left end name">{{$product->name}}</h5>
            <label class="checkbox-container end pull-right enabled" for="product-{{$product->id}}-enabled">
                <div class="checkbox">
                    @if($product->enabled)
                        <input checked class="product-enabled" id="product-{{$product->id}}-enabled" type="checkbox">
                    @else
                        <input class="product-enabled" id="product-{{$product->id}}-enabled" type="checkbox">
                    @endif
                    <span></span>
                </div>
            </label>
        </header>

        <article style="display: none;" class="row product-info module-content">
            <div class="row">
                <div class="col-xs-4 tight-left">
                    <a target="_blank" href="{{url(\App::getLocale() .'/store/' .$product->getPath())}}">
                        <img class="thumbnail" src="http://lorempixel.com/180/180/technics"/>
                    </a>
                </div>
                <div class="col-xs-8 tight-right">
                    <p>
                        <strong>@lang('admin.products_list_stock'): </strong>
                        <span>{{$product->stock}}</span>
                    </p>
                    <p>
                        <strong>@lang('admin.products_list_price'): </strong>
                        <span>{{$product->price}},- | {{$product->price * $product->vatgroup->amount}},-</span>
                    </p>
                    <p>
                        <strong>@lang('admin.sales'): </strong>
                        <span>{{$product->sales}}</span>
                    </p>

                    <div class="button-group product-controls">
                        <a class="button small edit" href="{{route('admin.products.edit', ['product' => $product])}}"><i class="fa fa-pencil"></i> @lang('admin.edit')</a>
                        <button class="button small primary move"><i class="fa fa-plane"></i> Move</button>
                        <a class="button small error" href="{{route('admin.products.delete', ['product' => $product])}}"><i class="fa fa-trash"></i> @lang('admin.delete')</a>
                    </div>
                </div>
            </div>

        </article>
    </li>
@endforeach