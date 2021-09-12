<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::post('login', 'Api\v1\Auth\LoginController@action'); //done
    Route::post('logout', 'Api\v1\Auth\LogoutController@action'); //done
    Route::post('reg', 'Api\v1\Auth\RegisterController@action'); //done

    Route::get('me', 'Api\v1\Auth\MeController@action'); //done
    Route::get('refresh', 'Api\v1\Auth\MeController@refresh'); //done
    Route::put('profile', 'Api\v1\Auth\MeController@update'); //done
    Route::put('change-password', 'Api\v1\Auth\MeController@changePassword'); //done
    Route::put('device', 'Api\v1\Auth\MeController@device'); //done
    Route::get('review', 'Api\v1\Auth\MeController@review'); //done
    Route::post('review', 'Api\v1\Auth\MeController@saveReview'); //done

    Route::post('get-otp', 'Api\v1\OtpController@getOtp'); //done
    Route::post('verify-otp', 'Api\v1\OtpController@verifyOtp'); //done
    Route::get('status/{name}/{flip?}', 'Api\v1\StatusController@index'); //done
    Route::get('status-order/{id}', 'Api\v1\StatusController@getOrderStatus'); //done
    Route::get('status/{name}/{flip?}', 'Api\v1\StatusController@index'); //done
    Route::get('settings', 'Api\v1\SettingController@index'); //done
    Route::get('banners', 'Api\v1\BannerController@index'); //done

    Route::get('locations', 'Api\v1\LocationController@index'); //done
    Route::get('locations/{id}/areas', 'Api\v1\LocationController@area'); //done
    Route::get('areas', 'Api\v1\AreaController@index'); //done

    Route::get('request-product', 'Api\v1\RequestProductController@index'); //done
    Route::post('request-product', 'Api\v1\RequestProductController@store'); //done
    Route::put('request-product/{id}', 'Api\v1\RequestProductController@update'); //done
    Route::get('request-product/{id}', 'Api\v1\RequestProductController@show'); //done
    Route::delete('request-product/{id}', 'Api\v1\RequestProductController@delete'); //done
    Route::get('product-category', 'Api\v1\ProductCategoryController@index'); //done

    Route::post('shop', 'Api\v1\ShopController@store'); //done
    Route::put('shop/{id}', 'Api\v1\ShopController@update'); //done
    Route::get('shop/{id}/show', 'Api\v1\ShopController@show'); //done
    Route::get('products', 'Api\v1\ProductController@index'); //done

    Route::get('shops/{shop}/categories', 'Api\v1\ShopCategoryController@action'); //done
    Route::get('shops/{shop}/categories/{category}', 'Api\v1\ShopCategoryProductController@action'); //done
    Route::get('shops/{shop}/products/{product}', 'Api\v1\ShopCategoryProductController@show'); //done
    Route::get('shops/{shop}/products', 'Api\v1\ShopProductController@action'); //done

    Route::get('shop-product/{shop_id}/shop/product', 'Api\v1\ShopProductController@product'); //done
    Route::get('shop-product/{shop_id}/shop/{id}/product', 'Api\v1\ShopProductController@show'); //done
    Route::post('shop-product/{shop_id}/shop/product', 'Api\v1\ShopProductController@store'); //done
    Route::put('shop-product/{shop_id}/shop/{id}/product', 'Api\v1\ShopProductController@update'); // done
    Route::delete('shop-product/{shop_id}/shop/{id}/product', 'Api\v1\ShopProductController@delete'); //done

    Route::get('request-withdraw', 'Api\v1\RequestWithdrawController@index'); //done
    Route::post('request-withdraw', 'Api\v1\RequestWithdrawController@store'); //done
    Route::put('request-withdraw/{id}', 'Api\v1\RequestWithdrawController@update'); //done
    Route::get('request-withdraw/{id}/show', 'Api\v1\RequestWithdrawController@show'); //done
    Route::delete('request-withdraw/{id}', 'Api\v1\RequestWithdrawController@delete'); //done

    Route::get('orders', 'Api\v1\OrderController@index'); //done
    Route::post('orders', 'Api\v1\OrderController@store'); //done
    Route::put('orders/{id}', 'Api\v1\OrderController@update'); //done
    Route::get('orders/{id}/show', 'Api\v1\OrderController@show'); //done
    Route::post('orders/payment', 'Api\v1\OrderController@orderPayment'); //done
    Route::get('orders/{id}/download-attachment', 'Api\v1\OrderController@attachment'); //done
    Route::get('orders/cancel/{id}', 'Api\v1\OrderController@orderCancel'); //done

    Route::get('shop-order', 'Api\v1\ShopOrderController@index'); //done
    Route::get('shop-order/{id}', 'Api\v1\ShopOrderController@show'); //done
    Route::post('shop-order', 'Api\v1\ShopOrderController@store'); //done
    Route::put('shop-order/{id}', 'Api\v1\ShopOrderController@update'); //done
    Route::get('customer-search', 'Api\v1\ShopOrderController@search'); //done

    Route::get('notification-order', 'Api\v1\NotificationOrderController@index'); //done
    Route::put('notification-order/{id}/update', 'Api\v1\NotificationOrderController@orderAccept'); //done
    Route::put('notification-order-product-receive/{id}/update', 'Api\v1\NotificationOrderController@OrderProductReceive'); //done
    Route::put('notification-order-status/{id}/update', 'Api\v1\NotificationOrderController@orderStatus'); //done
    Route::get('notification-order/{id}/show', 'Api\v1\NotificationOrderController@show'); //done
    Route::get('notification-order/history', 'Api\v1\NotificationOrderController@history'); //done

    Route::get('search/shops/{shop}', 'Api\v1\SearchController@shops'); //done
    Route::get('search/{shop}/shops/{product}/products', 'Api\v1\SearchController@shopProducts'); //done
    Route::get('best-selling/products', 'Api\v1\SearchController@bestSellingProducts'); //done
    Route::get('best-selling/categories', 'Api\v1\SearchController@bestSellingCategories'); //done

    Route::post('shop-owner-sales-report', 'Api\v1\ShopOwnerSalesReportController@index')->name('shop-owner-sales-report.index'); //done
    Route::get('transactions', 'Api\v1\TransactionController@index'); //done

});
