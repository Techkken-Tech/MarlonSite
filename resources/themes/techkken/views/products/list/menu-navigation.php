<?php

$menu=null;

foreach (app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id) as $_category) {
  if ($_category->slug) {
    if($_category->name=="Menu"){
        $menu=$_category;
    }
  }
}



?>
<div class="vertical-menu">
    
<div class="menu-title"><?php echo __('Menu'); ?></div>
  <?php if(count($menu->children)>0) :?>
    <?php foreach($menu->children as $subcategory): ?>
        <a href="/<?php echo $subcategory->url_path;?>" <?php if($category->id == $subcategory->id) echo 'class="active"'?> ><?php echo $subcategory->name; ?></a>
    <?php endforeach?>
  <?php endif?>
</div>