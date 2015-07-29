<?php
use Friluft\Category;

echo '<li><a href="' .route('home') .'">' .trans('store.home') .'</a></li>';

foreach (Category::root()->orderBy('order', 'asc')->get() as $cat) {
    echo '<li>' .$cat->renderMenuItem('', [], true);

        echo '<div class="nav-dropdown"><div class="inner clearfix">';
        foreach($cat->children as $child) {
            echo '<ul>';
                echo '<li>' .$child->renderMenuItem($cat->getPath(true), [], true);
                    if (count($child->children) > 0) {
                        echo '<ul class="nav vertical">';
                            echo $child->renderMenu($cat->getPath(true), 1);
                        echo '</ul>';
                    }
                echo '</li>';
            echo '</ul>';
        }
        echo '</div></div>';

    echo '</li>';
}
?>