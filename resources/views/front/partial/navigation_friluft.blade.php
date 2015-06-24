<?php
use Friluft\Category;

$root = ['bestselgere', 'friluft', 'fiske', 'trening', 'sykkel', 'lykteroglys', 'actionkamera', 'hund'];

foreach ($root as $cat) {
	$cat = Category::where('slug', '=', $cat)->first();

	if ($cat) {
		echo '<li>' .$cat->renderMenuItem('store');

			echo '<div class="nav-dropdown"><div class="inner clearfix">';
			$i = 0;
			foreach($cat->children as $child) {
				echo '<ul class="col-m-4 col-l-3 end">';
					echo '<li>' .$child->renderMenuItem($cat->getPath());
						echo '<ul class="nav vertical">';
							echo $child->renderMenu($cat->getPath(), 1);
						echo '</ul>';
					echo '</li>';
				echo '</ul>';
				$i++;

				if ($i % 3 == 0) {
					//echo '<hr class="visible-m">';
				}

				if ($i % 4 == 0) {
					//echo '<hr class="visible-l-up">';
				}
			}
			#echo $cat->renderMenu('store/' .$cat->slug, 3);
			echo '</div></div>';

		echo '</li>';
	}
}
?>