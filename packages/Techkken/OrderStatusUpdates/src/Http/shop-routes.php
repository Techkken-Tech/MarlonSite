<?php

Route::group([
        'prefix'     => 'orderstatusupdates',
        'middleware' => ['web', 'theme', 'locale', 'currency']
    ], function () {

        Route::get('/{id}', 'Techkken\OrderStatusUpdates\Http\Controllers\Shop\OrderStatusUpdatesController@index')->defaults('_config', [
            'view' => 'orderstatusupdates::shop.default.index',
        ])->name('shop.orderstatusupdates.index');

});