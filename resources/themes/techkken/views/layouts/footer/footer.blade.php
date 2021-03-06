<div class="footer">
    <div class="footer-content">
        <div class="footer-list-container">

            <?php
                $categories = [];

                foreach (app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id) as $category){
                    if ($category->slug)
                        array_push($categories, $category);
                }
            ?>

            @if (count($categories))
                <div class="list-container">
                    <span class="list-heading">Categories</span>

                    <ul class="list-group">
                        @foreach ($categories as $key => $category)   

                                @if(count($category->children)>0)
                                
                                    @foreach($category->children as $subcategory)
                                    <li class="nav-item" data-path="menu"><a href="/{{$subcategory->url_path}}">{{$subcategory->name}}</a></li>
                                    @endforeach
                                
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {!! DbView::make(core()->getCurrentChannel())->field('footer_content')->render() !!}

            <div class="list-container">
                <?php
                    $term = request()->input('term');

                    if (! is_null($term)) {
                        $serachQuery = 'term='.request()->input('term');
                    }
                ?>

                <label class="list-heading" for="locale-switcher">{{ __('shop::app.footer.locale') }}</label>
                <div class="form-container">
                    <div class="control-group">
                        <select class="control locale-switcher" id="locale-switcher" onchange="window.location.href = this.value" @if (count(core()->getCurrentChannel()->locales) == 1) disabled="disabled" @endif>

                            @foreach (core()->getCurrentChannel()->locales as $locale)
                                @if (isset($serachQuery))
                                    <option value="?{{ $serachQuery }}&locale={{ $locale->code }}" {{ $locale->code == app()->getLocale() ? 'selected' : '' }}>{{ $locale->name }}</option>
                                @else
                                    <option value="?locale={{ $locale->code }}" {{ $locale->code == app()->getLocale() ? 'selected' : '' }}>{{ $locale->name }}</option>
                                @endif
                            @endforeach

                        </select>
                    </div>
                </div>

                <div class="currency">
                    <label class="list-heading" for="currency-switcher">{{ __('shop::app.footer.currency') }}</label>
                    <div class="form-container">
                        <div class="control-group">
                            <select class="control locale-switcher" id="currency-switcher" onchange="window.location.href = this.value">

                                @foreach (core()->getCurrentChannel()->currencies as $currency)
                                    @if (isset($serachQuery))
                                        <option value="?{{ $serachQuery }}&currency={{ $currency->code }}" {{ $currency->code == core()->getCurrentCurrencyCode() ? 'selected' : '' }}>{{ $currency->code }}</option>
                                    @else
                                        <option value="?currency={{ $currency->code }}" {{ $currency->code == core()->getCurrentCurrencyCode() ? 'selected' : '' }}>{{ $currency->code }}</option>
                                    @endif
                                @endforeach

                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
