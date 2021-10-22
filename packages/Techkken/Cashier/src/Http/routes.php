<?php

Route::view('/cashier-route', 'techkken::cashier');

Route::group(['middleware' => ['web', 'admin_locale']], function () {
    Route::prefix(config('app.admin_url'))->group(function () {
        Route::get('/', 'Webkul\Admin\Http\Controllers\Controller@redirectToLogin');

        // Admin Routes
        Route::group(['middleware' => ['admin']], function () {
            Route::get('/techkken-cashier', 'Techkken\Cashier\Http\Controllers\CashierController@index')->defaults('_config', [
                'view' => 'techkken::cashier.index',
            ])->name('cashier.index');
        });
    });
});
