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
      <li class="nav-item active" data-path="home"><span><a href="{{ route('shop.home.index')}}">Home</a></span></li>
      @foreach($categories as $category)

      <li class="nav-item" data-path="menu"><span class="dropdown-toggle">{{$category->name}}</span>
        @if(count($category->children)>0)
          <ul class="dropdown-list">
            @foreach($category->children as $subcategory)
            <li class="nav-item active"><a href="/{{$subcategory->url_path}}">{{$subcategory->name}}</a></li>
            @endforeach
          </ul>
        @endif
      </li>

      @endforeach

      <li class="nav-item" data-path="about"><span><a href="/page/about-us">About</a></span></li>
    </ul>
  </div>
</nav>