<div class="product products-block" data-id="{{$product->id}}" data-price="{{$product->price}}" data-manufacturer="{{($product->manufacturer) ? $product->manufacturer->id : ''}}">
	<?php
	$img = $product->images()->first();
	if ($img == null) {
		$img = '';
	}else {
		$img = asset('images/products/' .$img->name);
	}

	$link = url($product->getPath());
	?>

	<div class="preview">
		<a href="{{$link}}" style="background-image: url('{{$img}}');" class="image">

            <div class="overlay">
                <div class="tags">
                    @foreach($product->tags as $tag)
                        <span class="tag" style="background-color: {{$tag->color}};"><i class="{{$tag->icon}}"></i> {{$tag->label}}</span>
                    @endforeach
                </div>
            </div>
        </a>
	</div>

	<header class="header clearfix">
		<div class="title">
            @if(isset($product->manufacturer))
                <h6 class="manufacturer end">{{$product->manufacturer->name}}</h6>
            @endif
			<h5 class="name end">{{$product->name}}</h5>
		</div>

		<h3 class="price end">{{ceil($product->price * $product->vatgroup->amount)}},-</h3>
	</header>

	<footer class="footer">
		<hr class="divider shadow">
		<a class="buy" href="{{$link}}"><i class="fa fa-cart-plus"></i></a>
		<a class="share" href="#"><i class="fa fa-share-alt"></i></a>
	</footer>
</div>