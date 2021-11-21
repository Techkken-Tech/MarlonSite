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
            Route::get('/techkken-cashier-view/{id}', 'Techkken\Cashier\Http\Controllers\CashierController@view')->defaults('_config', [
                'view' => 'techkken::cashier.view',
            ])->name('cashier.view');
            Route::get('/techkken-cashier-viewOrder/{id}', 'Techkken\Cashier\Http\Controllers\CashierController@viewOrder')->name('cashier.viewOrder');
            Route::get('/techkken-cashier-processOrder/{id}', 'Techkken\Cashier\Http\Controllers\CashierController@ProcessOrder')->name('cashier.processOrder');
        });
    });
});
