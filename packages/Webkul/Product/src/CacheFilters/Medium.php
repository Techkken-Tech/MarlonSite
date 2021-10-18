<?php

namespace Webkul\Product\CacheFilters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Medium implements FilterInterface
{
    /**
     * Apply filter.
     * 
     * @param  \Intervention\Image\Image  $image
     * @return \Intervention\Image\Image
     */
    public function applyFilter(Image $image)
    {
<<<<<<< HEAD
        $width = core()->getConfigData('catalog.products.cache-medium-image.width') != '' ? core()->getConfigData('catalog.products.cache-medium-image.width') : 280;

        $height = core()->getConfigData('catalog.products.cache-medium-image.height') != '' ? core()->getConfigData('catalog.products.cache-medium-image.height') : 280;

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        return $image->resizeCanvas($width, $height, 'center', false, '#fff');
=======
        // // $width = core()->getConfigData('catalog.products.cache-medium-image.width') != '' ? core()->getConfigData('catalog.products.cache-medium-image.width') : 280;

        // // $height = core()->getConfigData('catalog.products.cache-medium-image.height') != '' ? core()->getConfigData('catalog.products.cache-medium-image.height') : 280;

        // $image->resize(, $height, function ($constraint) {
        //     $constraint->aspectRatio();
        // });

        return $image;
>>>>>>> f94fe6c73f62d8f4b86e325c892b9f24254029a0
    }
}
