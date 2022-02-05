<?php

Route::group([
        'prefix'        => 'admin/orderstatusupdates',
        'middleware'    => ['web', 'admin']
    ], function () {

        Route::get('', 'Techkken\OrderStatusUpdates\Http\Controllers\Admin\OrderStatusUpdatesController@index')->defaults('_config', [
            'view' => 'orderstatusupdates::admin.index',
        ])->name('admin.orderstatusupdates.index');

});