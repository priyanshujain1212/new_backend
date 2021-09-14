<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['installed']], function () {
    Auth::routes(['verify' => false]);
});

Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'middleware' => ['web', 'install']], function () {
    Route::post('environment/saveWizard', [
        'as'   => 'environmentSaveWizard',
        'uses' => 'EnvironmentController@saveWizard',
    ]);

    Route::get('purchase-code', [
        'as'   => 'purchase_code',
        'uses' => 'PurchaseCodeController@index',
    ]);

    Route::post('purchase-code', [
        'as'   => 'purchase_code.check',
        'uses' => 'PurchaseCodeController@action',
    ]);
});

Route::group(['middleware' => ['installed'], 'namespace' => 'Frontend'], function () {
    Route::get('/', 'WebController@index')->name('home');
    Route::get('shop/{shop}', 'ShopController')->name('shop.show');
    Route::get('shop/{shop}/product/{product}', 'ShopProductController')->name('shop.product.details');

    Route::get('cart', 'CartController@index')->name('cart.index');
    Route::post('cart', 'CartController@store')->name('cart.store');
    Route::get('cart/{id}', 'CartController@remove')->name('cart.remove');
    Route::post('cart-quantity', 'CartController@quantity')->name('cart.quantity');

    Route::get('checkout', 'CheckoutController@index')->name('checkout.index')->middleware('auth');
    Route::post('checkout', 'CheckoutController@store')->name('checkout.store')->middleware('auth');

    Route::get('account/profile', 'AccountController@index')->name('account.profile')->middleware('auth');
    Route::get('account/password', 'AccountController@getPassword')->name('account.password')->middleware('auth');
    Route::put('account/password', 'AccountController@password_update')->name('account.password.update')->middleware('auth');
    Route::get('account/update', 'AccountController@profileUpdate')->name('account.profile.index')->middleware('auth');
    Route::put('account/update/{profile}', 'AccountController@update')->name('account.profile.update')->middleware('auth');
    Route::get('account/order', 'AccountController@getOrder')->name('account.order')->middleware('auth');
    Route::get('account/get-order', 'AccountController@getOrderList')->name('account.get-order')->middleware('auth');
    Route::get('account/order/{id}', 'AccountController@orderShow')->name('account.order.show')->middleware('auth');
    Route::get('account/order-cancel/{id}', 'AccountController@orderCancel')->name('account.order.cancel')->middleware('auth');
    Route::get('account/order-file/{id}', 'AccountController@getDownloadFile')->name('account.order.file')->middleware('auth');

    Route::get('account/transaction', 'AccountController@getTransactions')->name('account.transaction')->middleware('auth');
    Route::get('account/review', 'AccountController@review')->name('account.review')->middleware('auth');
    Route::get('account/get-review', 'AccountController@getReview')->name('account.get-review')->middleware('auth');

    Route::get('account/shop-product-ratings/{shop}/{product}', 'AccountController@shopProductRatings')->name('account.shop-product-ratings')->middleware('auth');
    Route::post('account/shop-product-ratings-update', 'AccountController@shopProductRatingsUpdate')->name('account.shop-product-ratings-update')->middleware('auth');

    Route::get('/search', 'SearchController@filter')->name('search');
    Route::get('/{shop}/products/search', 'SearchController@filterProduct')->name('search-product');
    Route::get('/privacy', 'PrivacyController')->name('privacy');
    Route::get('/terms', 'TermController')->name('terms');
    Route::get('/contact', 'ContactController')->name('contact');
    Route::post('/contact', 'ContactController@store')->name('contact.store');

    Route::get('page/{slug}', 'FrontendPageController@index')->name('page');

    Route::post('areas', 'AreaController@index')->name('area.index');
});

Route::redirect('/admin', '/admin/dashboard')->middleware('backend_permission');

Route::group(['prefix' => 'admin', 'middleware' => ['installed'], 'namespace' => 'Admin', 'as' => 'admin.'], function () {
    Route::get('login', 'Auth\LoginController@showLoginForm');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'installed', 'backend_permission'], 'namespace' => 'Admin', 'as' => 'admin.'], function () {

    Route::get('dashboard', 'DashboardController@index')->name('dashboard.index');
    Route::post('day-wise-income-order', 'DashboardController@dayWiseIncomeOrder')->name('dashboard.day-wise-income-order');

    Route::get('profile', 'ProfileController@index')->name('profile');
    Route::put('profile/update/{profile}', 'ProfileController@update')->name('profile.update');
    Route::put('profile/change', 'ProfileController@change')->name('profile.change');

    Route::post('handlePaytmRequest', 'paytmcontroller@handlePaytmRequest')->name('paytm.handlePaytmRequest');
    Route::get('paytm-callback', 'PaytmController@paytmCallback');

    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('/', 'SettingController@index')->name('index');
        Route::post('/', 'SettingController@siteSettingUpdate')->name('site-update');
        Route::get('sms', 'SettingController@smsSetting')->name('sms');
        Route::post('sms', 'SettingController@smsSettingUpdate')->name('sms-update');
        Route::get('payment', 'SettingController@paymentSetting')->name('payment');
        Route::post('payment', 'SettingController@paymentSettingUpdate')->name('payment-update');
        Route::get('email', 'SettingController@emailSetting')->name('email');
        Route::post('email', 'SettingController@emailSettingUpdate')->name('email-update');
        Route::get('notification', 'SettingController@notificationSetting')->name('notification');
        Route::post('notification', 'SettingController@notificationSettingUpdate')->name('notification-update');
        Route::get('social-login', 'SettingController@socialLoginSetting')->name('social-login');
        Route::post('social-login', 'SettingController@socialLoginSettingUpdate')->name('social-login-update');
        Route::get('otp', 'SettingController@otpSetting')->name('otp');
        Route::post('otp', 'SettingController@otpSettingUpdate')->name('otp-update');
        Route::get('homepage', 'SettingController@homepageSetting')->name('homepage');
        Route::post('homepage', 'SettingController@homepageSettingUpdate')->name('homepage-update');

        Route::get('homepage', 'SettingController@homepageSetting')->name('homepage');
        Route::post('homepage', 'SettingController@homepageSettingUpdate')->name('homepage-update');

        Route::get('social', 'SettingController@socialSetting')->name('social');
        Route::post('social', 'SettingController@socialSettingUpdate')->name('social-update');
    });

    Route::resource('page', 'PageController');
    Route::get('get-page', 'PageController@getPage')->name('page.get-page');

    Route::resource('location', 'LocationController');
    Route::get('get-location', 'LocationController@getLocation')->name('location.get-location');

    Route::get('rating', 'RatingController@index')->name('rating.index');
    Route::put('rating/{id}', 'RatingController@update')->name('rating.update');
    Route::get('get-rating', 'RatingController@getRating')->name('rating.get-rating');

    Route::resource('area', 'AreaController');
    Route::get('get-area', 'AreaController@getArea')->name('area.get-area');

    Route::resource('category', 'CategoryController');
    Route::get('get-category', 'CategoryController@getCategory')->name('category.get-category');

    Route::resource('banner', 'BannerController');
    Route::post('sort-banner', 'BannerController@sortBanner')->name('sort.banner');

    Route::resource('request-withdraw', 'RequestWithdrawController');
    Route::get('get-request-withdraw', 'RequestWithdrawController@getRequestWithdraw')->name('request-withdraw.get-request-withdraw');

    Route::resource('administrators', 'AdministratorController');
    Route::get('get-administrators', 'AdministratorController@getAdministrators')->name('administrators.get-administrators');

    Route::resource('customers', 'CustomerController');
    Route::get('get-customers', 'CustomerController@getCustomers')->name('customers.get-customers');

    Route::resource('delivery-boys', 'DeliveryBoyController');
    Route::get('get-delivery-boys', 'DeliveryBoyController@getDeliveryBoy')->name('delivery-boys.get-delivery-boys');

    Route::get('get-order-history', 'DeliveryBoyController@history')->name('delivery-boys.get-order-history');

    Route::resource('section', 'SectionController');
    Route::get('get-section', 'SectionController@getSection')->name('section.get-section');

    Route::resource('shop', 'ShopController');

    Route::get('order-notification', 'OrderNotificationController@index')->name('order-notification.index');
    Route::get('order-notification/{id}/accept/{deliveryStatus}', 'OrderNotificationController@accept')->name('order-notification.accept');
    Route::get('get-order-notification', 'OrderNotificationController@getOrderNotification')->name('order-notification.get-order-notifications');

    Route::resource('collection', 'CollectionController');
    Route::get('get-collection', 'CollectionController@getCollection')->name('collection.get-collection');
    Route::post('get-collection-delivery-boy', 'CollectionController@getDeliveryBoy')->name('collection.get-delivery-boy');

    Route::post('orders/{order}/product-receive', 'OrderController@productReceive')->name('orders.product-receive');

    Route::get('shop/{shop}/products', 'ShopController@products')->name('shop.products');
    Route::get('shop/{shop}/products/create', 'ShopController@productAdd')->name('shop.products.create');
    Route::post('shop/{shop}/products/create', 'ShopController@productStore')->name('shop.products.store');
    Route::get('shop/{shop}/products/{shopproduct}/edit', 'ShopController@shopProductEdit')->name('shop.shopproduct.edit');
    Route::put('shop/{shop}/products/{shopproduct}/update', 'ShopController@shopProductUpdate')->name('shop.products.update');
    Route::delete('shop/{shop}/products/{shopproduct}/delete', 'ShopController@shopProductDelete')->name('shop.shopproduct.delete');

    // Route::post('shop/{shop}/products/attach', 'ShopController@productAttach')->name('shop.product.attach');




    Route::post('shopstore', 'ShopController@shopstore')->name('shop.shopstore');
    Route::get('shopedit/{shop}', 'ShopController@shopedit')->name('shop.shopedit');
    Route::put('shopupdate/{shop}', 'ShopController@shopupdate')->name('shop.shopupdate');
    Route::get('get-shop', 'ShopController@getShop')->name('shop.get-shop');
    Route::get('get-shop-product', 'ShopController@getShopProduct')->name('shop.get-shop-product');
    Route::post('get-shop', 'ShopController@getArea')->name('shop.get-area');

    Route::resource('products', 'ProductController');
    Route::post('getMedia', 'ProductController@getMedia')->name('products.getMedia');
    Route::post('storeMedia', 'ProductController@storeMedia')->name('products.storeMedia');
    Route::post('storeMedia/{product}', 'ProductController@updateMedia')->name('products.updateMedia');
    Route::post('removeMedia', 'ProductController@removeMedia')->name('products.removeMedia');
    Route::post('deleteMedia', 'ProductController@deleteMedia')->name('products.deleteMedia');
    Route::get('get-products', 'ProductController@getProduct')->name('products.get-product');

    Route::resource('request-products', 'RequestProductController');
    Route::get('get-request-products', 'RequestProductController@getRequestProduct')->name('request-products.get-request-product');
    Route::post('request-product/getMedia', 'RequestProductController@getMedia')->name('request-products.getMedia');
    Route::post('request-product/storeMedia', 'RequestProductController@storeMedia')->name('request-products.storeMedia');
    Route::post('request-product/storeMedia/{product}', 'RequestProductController@updateMedia')->name('request-products.updateMedia');
    Route::post('request-product/removeMedia', 'RequestProductController@removeMedia')->name('request-products.removeMedia');
    Route::post('request-product/deleteMedia', 'RequestProductController@deleteMedia')->name('request-products.deleteMedia');

    Route::resource('orders', 'OrderController');
    Route::get('orders/{order}/delivery', 'OrderController@delivery')->name('orders.delivery');
    Route::get('get-orders', 'OrderController@getOrder')->name('orders.get-orders');
    Route::get('orders/order-file/{id}', 'OrderController@getDownloadFile')->name('orders.order-file');

    Route::resource('updates', 'UpdateController');
    Route::get('get-updates', 'UpdateController@getUpdates')->name('updates.get-updates');
    Route::get('checking-updates', 'UpdateController@checking')->name('updates.checking-updates');
    Route::get('update', 'UpdateController@update')->name('updates.update');
    Route::get('update-log', 'UpdateController@log')->name('updates.update-log');

    Route::get('payment', 'PaymentController@index')->name('payment.index');
    Route::get('payment/invoice', 'PaymentController@invoice')->name('payment.invoice');
    Route::get('payment/cancel', 'PaymentController@cancel')->name('payment.cancel');

    Route::get('transaction', 'TransactionController@index')->name('transaction.index');
    Route::get('get-transaction', 'TransactionController@getTransaction')->name('transaction.get-transaction');

    Route::get('shop-owner-sales-report', 'ShopOwnerSalesReportController@index')->name('shop-owner-sales-report.index');
    Route::post('shop-owner-sales-report', 'ShopOwnerSalesReportController@index')->name('shop-owner-sales-report.index');

    Route::get('admin-commission-report', 'AdminCommissionReportController@index')->name('admin-commission-report.index');
    Route::post('admin-commission-report', 'AdminCommissionReportController@index')->name('admin-commission-report.index');

    Route::get('credit-balance-report', 'CreditBalanceReportController@index')->name('credit-balance-report.index');
    Route::post('credit-balance-report', 'CreditBalanceReportController@index')->name('credit-balance-report.index');
    Route::post('get-role-user', 'CreditBalanceReportController@getUsers')->name('get-role-user');

    Route::get('cash-on-delivery-order-balance-report', 'CashOnDeliveryOrderBalanceReportController@index')->name('cash-on-delivery-order-balance-report.index');
    Route::post('cash-On-delivery-order-balance-report', 'CashOnDeliveryOrderBalanceReportController@index')->name('cash-on-delivery-order-balance-report.index');

    Route::resource('role', 'RoleController');
    Route::post('role/save-permission/{id}', 'RoleController@savePermission')->name('role.save-permission');

    Route::resource('withdraw', 'WithdrawController');
    Route::get('withdraw/create/{id?}', 'WithdrawController@create')->name('withdraw.create');
    Route::get('get-withdraw', 'WithdrawController@getWithdraw')->name('withdraw.get-withdraw');
    Route::post('get-user-info', 'WithdrawController@getUserInfo')->name('withdraw.get-user-info');

});

Route::get('webview/paypal/{id}', 'Admin\WebviewController@paypal')->name('webview.paypal');
Route::post('webview/paypal/payment', 'Admin\WebviewController@paypalpayment')->name('webview.paypal.payment');
Route::get('webview/paypal/{id}/return', 'Admin\WebviewController@paypalReturn')->name('webview.paypal.return');
Route::get('webview/paypal/{id}/cancel', 'Admin\WebviewController@paypalCancel')->name('webview.paypal.cancel');

Route::get('webview/stripe', 'Admin\WebviewController@stripe')->name('webview.stripe');
Route::get('webview/stripe', 'Admin\WebviewController@stripe')->name('webview.stripe');

Route::get('paypal/ec-checkout', 'Admin\PayPalController@getExpressCheckout');
Route::get('paypal/ec-checkout-success', 'Admin\PayPalController@getExpressCheckoutSuccess');
Route::get('paypal/adaptive-pay', 'Admin\PayPalController@getAdaptivePay');
Route::post('paypal/notify', 'Admin\PayPalController@notify');
