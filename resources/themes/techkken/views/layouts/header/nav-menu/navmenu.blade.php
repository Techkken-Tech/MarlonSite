{!! view_render_event('bagisto.shop.layout.header.category.before') !!}

<?php
$categories = [];

foreach (app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id) as $category) {
    if ($category->slug) {
        array_push($categories, $category);
    }
}

?>
<nav class="navbar">
  <div class="container-fluid">
    <ul class="nav navbar-nav">
      <li class="active"><a href="#">Home</a></li>
      <li><a href="#">Menu</a></li>
      <li><a href="#">About</a></li>
    </ul>
  </div>
</nav>