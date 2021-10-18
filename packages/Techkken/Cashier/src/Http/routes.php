<?php

use Techkken\Cashier\Http\Controllers\HelloWorldController;

Route::view('/cashier-route', 'techkken::cashier');

Route::get('/test-hello', [HelloWorldController::class, 'index'])
    ->defaults('_config', ['view' => 'techkken::helloworld.index'])
    ->name('helloworld.index');

Route::get('/test101-hello', [HelloWorldController::class, 'index'])
    ->defaults('_config', ['view' => 'techkken::helloworld.index2']);