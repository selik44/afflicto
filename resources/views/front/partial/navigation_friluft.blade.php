<?php
use Friluft\Category;

echo '<li><a href="' .route('home') .'">Home</a></li>';

foreach (Category::root()->orderBy('order', 'asc')->get() as $cat) {
    echo '<li>' .$cat->renderMenuItem('store', [], true);

        echo '<div class="nav-dropdown"><div class="inner clearfix">';
        foreach($cat->children as $child) {
            echo '<ul>';
                echo '<li>' .$child->renderMenuItem($cat->getPath(), [], true);
                    if (count($child->children) > 0) {
                        echo '<ul class="nav vertical">';
                            echo $child->renderMenu($cat->getPath(), 1);
                        echo '</ul>';
                    }
                echo '</li>';
            echo '</ul>';
        }
        echo '</div></div>';

    echo '</li>';
}
?>