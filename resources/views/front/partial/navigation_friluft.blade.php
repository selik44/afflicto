<?php
	
	use Friluft\Category;
	
	$root = ['bestselgere', 'friluft', 'fiske', 'lopetoy', 'sykkel', 'lykteroglys', 'actionkamera', 'hund'];

	foreach ($root as $cat) {
		$cat = Category::where('slug', '=', $cat)->first();

		if ($cat) {
			echo '<li>' .$cat->renderMenuItem('store');

				echo '<div class="nav-dropdown"><div class="inner clearfix">';
					$i = 0;
					foreach($cat->children as $child) {
						echo '<div class="col-m-4 col-l-3">';
							echo '<ul class="nav vertical">';
								echo '<li class="heading">' .$child->renderMenuItem($cat->getPath()) .'</li>';
								echo $child->renderMenu($cat->getPath(), 1);
							echo '</ul>';
						echo '</div>';
						$i++;

						if ($i % 3 == 0) {
							echo '<hr class="visible-m">';
						}

						if ($i % 4 == 0) {
							echo '<hr class="visible-l-up">';
						}
					}
					#echo $cat->renderMenu('store/' .$cat->slug, 3);
				echo '</div></div>';

			echo '</li>';
		}
	}
?>
<!--
<li><a href="{{url('/')}}">Hjem</a></li>
<li><a href="{{url('/store/bestselgere')}}">Bestselgere</a></li>
<li><a href="{{url('store/friluft')}}">Friluft</a>
	<div class="nav-dropdown">
		<div class="inner">
			<div class="col-m-4">
				<ul class="nav vertical">
					<li class="heading">Friluft</li>
					<li><a href="{{url('/store/telt')}}">Telt</a></li>
					<li><a href="{{url('/store/kokeutstyr')}}">Turkjøkken</a></li>
					<li><a href="{{url('/store/treartikler')}}">Treartikler</a></li>
					<li><a href="{{url('/store/termos-flasker')}}">Termos & Flasker</a></li>
					<li><a href="{{url('/store/turutstyr')}}">Turutstyr</a></li>
					<li><a href="{{url('/store/fjelldukponcho')}}">Fjellduk/Poncho</a></li>
					<li><a href="{{url('/store/lykter')}}">Lykter</a></li>
				</ul>
			</div>
		</div>
	</div>
</li>
<li><a href="{{url('store/fiske')}}">Fiske</a></li>
<li><a href="{{url('store/lopetoy')}}">Trening</a></li>
<li><a href="{{url('store/sykkel')}}">Sykkel</a></li>
<li><a href="{{url('store/lykteroglys')}}">Lykter</a></li>
<li><a href="{{url('store/actionkamera')}}">ActionCam</a></li>
<li><a href="{{url('store/hund')}}">Hund</a></li>
-->