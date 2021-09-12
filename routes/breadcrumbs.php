<?php

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

// Home
Breadcrumbs::for ('dashboard', function ($trail) {
    $trail->push(trans('validation.attributes.dashboard'), route('admin.dashboard.index'));
});

Breadcrumbs::for ('profile', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.profile'));
});

// Dashboard / Setting
Breadcrumbs::for ('setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.settings'));
});

// Dashboard / Email Setting
Breadcrumbs::for ('sms-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.sms_settings'));
});

// Dashboard / Email Setting
Breadcrumbs::for ('emailsetting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.emailsettings'));
});

// Dashboard / SMS Setting
Breadcrumbs::for ('smssetting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.smssetting'));
});

// Dashboard / SMS Setting
Breadcrumbs::for ('notificationsetting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.notificationsetting'));
});

// Dashboard / Payment Setting
Breadcrumbs::for ('payment-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.payment_settings'));
});

// Dashboard / Location
Breadcrumbs::for ('locations', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.locations'), route('admin.location.index'));
});

// Dashboard / Location / Add
Breadcrumbs::for ('location/add', function ($trail) {
    $trail->parent('locations');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Location / Edit
Breadcrumbs::for ('location/edit', function ($trail) {
    $trail->parent('locations');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Area
Breadcrumbs::for ('areas', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.areas'), route('admin.area.index'));
});

// Dashboard / Area / Add
Breadcrumbs::for ('area/add', function ($trail) {
    $trail->parent('areas');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Area / Edit
Breadcrumbs::for ('area/edit', function ($trail) {
    $trail->parent('areas');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / category
Breadcrumbs::for ('categories', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.categories'), route('admin.category.index'));
});

// Dashboard / categories / Add
Breadcrumbs::for ('categories/add', function ($trail) {
    $trail->parent('categories');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / categories / Edit
Breadcrumbs::for ('categories/edit', function ($trail) {
    $trail->parent('categories');
    $trail->push(trans('validation.attributes.edit'));
});

/* Product breadcrumbs */
// Dashboard / category
Breadcrumbs::for ('products', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.products'), route('admin.products.index'));
});

// Dashboard / products / Add
Breadcrumbs::for ('products/view', function ($trail) {
    $trail->parent('products');
    $trail->push(trans('validation.attributes.view'));
});

Breadcrumbs::for ('products/add', function ($trail) {
    $trail->parent('products');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / products / Edit
Breadcrumbs::for ('products/edit', function ($trail) {
    $trail->parent('products');
    $trail->push(trans('validation.attributes.edit'));
});
/* Product breadcrumbs ends */

/* Product breadcrumbs */
// Dashboard / request-products
Breadcrumbs::for ('request-products', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.request-products'), route('admin.request-products.index'));
});

// Dashboard / request-products / Add
Breadcrumbs::for ('request-products/view', function ($trail) {
    $trail->parent('request-products');
    $trail->push(trans('validation.attributes.view'));
});

Breadcrumbs::for ('request-products/add', function ($trail) {
    $trail->parent('request-products');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / request-products / Edit
Breadcrumbs::for ('request-products/edit', function ($trail) {
    $trail->parent('request-products');
    $trail->push(trans('validation.attributes.edit'));
});
/* Product breadcrumbs ends */

// Dashboard / Shop
Breadcrumbs::for ('shops', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.shops'), route('admin.shop.index'));
});

// Dashboard / Shop / Add
Breadcrumbs::for ('shop/add', function ($trail) {
    $trail->parent('shops');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('shop/edit', function ($trail) {
    $trail->parent('shops');
    $trail->push(trans('validation.attributes.edit'));
});
// Dashboard / Shop / View
Breadcrumbs::for ('shop/view', function ($trail) {
    $trail->parent('shops');
    $trail->push(trans('validation.attributes.view'));
});

Breadcrumbs::for ('shop/shop-product', function ($trail) {
    $trail->parent('shops');
    $trail->push(trans('validation.attributes.shopproducts'));
});

Breadcrumbs::for ('shop-product-add', function ($trail, $shop) {
    $trail->parent('shops');
    $trail->push(trans('validation.attributes.shopproducts'), route('admin.shop.products', $shop));
    $trail->push(trans('validation.attributes.add'));
});

Breadcrumbs::for ('shop-product-edit', function ($trail, $shop) {
    $trail->parent('shops');
    $trail->push(trans('validation.attributes.shopproducts'), route('admin.shop.products', $shop));
    $trail->push(trans('validation.attributes.edit'));
});

Breadcrumbs::for ('orders', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.orders'), route('admin.orders.index'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('orders/edit', function ($trail) {
    $trail->parent('orders');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('orders/view', function ($trail) {
    $trail->parent('orders');
    $trail->push(trans('validation.attributes.view'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('orders/delivery', function ($trail) {
    $trail->parent('orders');
    $trail->push(trans('validation.attributes.delivery'));
});

// Dashboard / Shop
Breadcrumbs::for ('shop-product', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.shop-product'), route('admin.shop-product.index'));
});

// Dashboard / Shop-product / Add
Breadcrumbs::for ('shop-product/add', function ($trail) {
    $trail->parent('shop-product');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Shop-product / Edit
Breadcrumbs::for ('shop-product/edit', function ($trail) {
    $trail->parent('shop-product');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / User
Breadcrumbs::for ('administrators', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.administrators'), route('admin.administrators.index'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('administrators/add', function ($trail) {
    $trail->parent('administrators');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('administrators/edit', function ($trail) {
    $trail->parent('administrators');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('administrators/view', function ($trail) {
    $trail->parent('administrators');
    $trail->push(trans('validation.attributes.view'));
});

// Dashboard / User
Breadcrumbs::for ('customers', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.customers'), route('admin.customers.index'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('customers/edit', function ($trail) {
    $trail->parent('customers');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('customers/view', function ($trail) {
    $trail->parent('customers');
    $trail->push(trans('validation.attributes.view'));
});

// Dashboard / User
Breadcrumbs::for ('delivery-boys', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.delivery-boys'), route('admin.delivery-boys.index'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('delivery-boys/add', function ($trail) {
    $trail->parent('delivery-boys');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('delivery-boys/edit', function ($trail) {
    $trail->parent('delivery-boys');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Shop / Edit
Breadcrumbs::for ('delivery-boys/view', function ($trail) {
    $trail->parent('delivery-boys');
    $trail->push(trans('validation.attributes.view'));
});

Breadcrumbs::for ('updates', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.updates'), route('admin.updates.index'));
});

Breadcrumbs::for ('transaction', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.transaction'), route('admin.transaction.index'));
});

Breadcrumbs::for ('shop-owner-sales-report', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.shop-owner-sales-report'), route('admin.shop-owner-sales-report.index'));
});
Breadcrumbs::for ('admin-commission-report', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.admin-commission-report'), route('admin.admin-commission-report.index'));
});

Breadcrumbs::for ('credit-balance-report', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.credit-balance-report'), route('admin.credit-balance-report.index'));
});

Breadcrumbs::for ('cash-on-delivery-order-balance-report', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.cash-on-delivery-order-balance-report'), route('admin.cash-on-delivery-order-balance-report.index'));
});

// Dashboard / Role
Breadcrumbs::for ('roles', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.roles'), route('admin.role.index'));
});

// Dashboard / Role / Add
Breadcrumbs::for ('role/add', function ($trail) {
    $trail->parent('roles');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Role / Edit
Breadcrumbs::for ('role/edit', function ($trail) {
    $trail->parent('roles');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Role / View
Breadcrumbs::for ('role/view', function ($trail) {
    $trail->parent('roles');
    $trail->push(trans('validation.attributes.view'));
});

// Dashboard / Banners
Breadcrumbs::for ('banners', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.banners'), route('admin.banner.index'));
});

// Dashboard / Banner / Add
Breadcrumbs::for ('banners/add', function ($trail) {
    $trail->parent('banners');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Banner / Edit
Breadcrumbs::for ('banners/edit', function ($trail) {
    $trail->parent('banners');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Collection
Breadcrumbs::for ('collections', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.collections'), route('admin.collection.index'));
});

// Dashboard / Collection / Add
Breadcrumbs::for ('collection/add', function ($trail) {
    $trail->parent('collections');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Collection / Edit
Breadcrumbs::for ('collection/edit', function ($trail) {
    $trail->parent('collections');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Collection / View
Breadcrumbs::for ('collection/view', function ($trail) {
    $trail->parent('collections');
    $trail->push(trans('validation.attributes.view'));
});

// Dashboard / Order Notification
Breadcrumbs::for ('order-notifications', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.order_notifications'), route('admin.order-notification.index'));
});

// Dashboard / Order Notification / Add
Breadcrumbs::for ('order-notification/add', function ($trail) {
    $trail->parent('order-notifications');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Order Notification / Edit
Breadcrumbs::for ('order-notification/edit', function ($trail) {
    $trail->parent('order-notifications');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Order Notification / View
Breadcrumbs::for ('order-notification/view', function ($trail) {
    $trail->parent('order-notifications');
    $trail->push(trans('validation.attributes.view'));
});

// Dashboard / Withdraw
Breadcrumbs::for ('withdraw', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.withdraw'), route('admin.withdraw.index'));
});

// Dashboard / withdraw / Add
Breadcrumbs::for ('withdraw/add', function ($trail) {
    $trail->parent('withdraw');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / withdraw / Edit
Breadcrumbs::for ('withdraw/edit', function ($trail) {
    $trail->parent('withdraw');
    $trail->push(trans('validation.attributes.edit'));
});

// Dashboard / Request Withdraw
Breadcrumbs::for ('request-withdraw', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.request-withdraw'), route('admin.request-withdraw.index'));
});

// Dashboard / Request Withdraw / Add
Breadcrumbs::for ('request-withdraw/add', function ($trail) {
    $trail->parent('request-withdraw');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Request Withdraw / Edit
Breadcrumbs::for ('request-withdraw/edit', function ($trail) {
    $trail->parent('request-withdraw');
    $trail->push(trans('validation.attributes.edit'));
});


// Dashboard / Page
Breadcrumbs::for ('pages', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.pages'), route('admin.page.index'));
});

// Dashboard / Page / Add
Breadcrumbs::for ('pages/add', function ($trail) {
    $trail->parent('pages');
    $trail->push(trans('validation.attributes.add'));
});

// Dashboard / Page / Edit
Breadcrumbs::for ('pages/edit', function ($trail) {
    $trail->parent('pages');
    $trail->push(trans('validation.attributes.edit'));
});
// Setting Module
Breadcrumbs::for ('site-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.site_settings'));
});

// Setting Module
Breadcrumbs::for ('email-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.email_settings'));
});

// Setting Module
Breadcrumbs::for ('notification-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.notification_settings'));
});

// Setting Module
Breadcrumbs::for ('social-login-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.social_login_settings'));
});

// Setting Module
Breadcrumbs::for ('otp-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.otp_settings'));
});

// Setting Module
Breadcrumbs::for ('homepage-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.homepage_settings'));
});

// Setting Module
Breadcrumbs::for ('social-setting', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.social_settings'));
});


Breadcrumbs::for ('ratings', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('validation.attributes.ratings'));
});
