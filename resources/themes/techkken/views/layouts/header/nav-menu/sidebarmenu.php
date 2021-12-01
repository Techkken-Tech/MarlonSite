<?php
$categories = [];

foreach (app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id) as $category) {
  if ($category->slug) {
    array_push($categories, $category);
  }
}

?>
<!-- Sidebar -->
<div id="overlay-level-2">
<div id="sidebar-wrapper">
    <nav>
            <ul class="sidebar-nav nav">
              <li class="nav-item active"><span><a href="<?php echo  route('shop.home.index');?>">Home</a></span></li>
              <?php foreach($categories as $category):?>
                <li class="nav-item"><span><a href="/<?php echo $category->url_path; ?>"><?php echo $category->name;?></a></span>
                  <?php if(count($category->children)>0):?>
                    <ul class="nav">
                      <?php foreach($category->children as $subcategory):?>
                        <li class="nav-item"><span><a href="/<?php echo $subcategory->url_path; ?>"><?php echo $subcategory->name;?></a></span></li>
                      <?php endforeach?>
                    </ul>
                  <?php endif?>
                </li>
              <?php endforeach?>

              <li class="nav-item "><span><a href="/about">About</a></span></li>
            </ul>
    </nav>
  </div>

  <div class="dark-page-overlay">
  </div>
</div>