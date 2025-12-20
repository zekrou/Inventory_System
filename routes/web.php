<?php

use Illuminate\Support\Facades\Route;

// ...existing routes...

Route::middleware(['auth'])->group(function () {
    // ...existing routes...
    
    // Order routes
    Route::get('/orders/{orderId}', 'OrderController@show')->name('orders.show');
    Route::get('/orders/{orderId}/invoice', 'OrderController@downloadInvoice')->name('orders.downloadInvoice');
    
    // Customer routes
    Route::get('/customers/{customerId}/orders', 'CustomerController@viewOrders')->name('customers.orders');
    
    // ...existing routes...
});