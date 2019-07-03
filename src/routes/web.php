<?php

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*$request = app()->make('request');
$currentHost = $request->header('host');
$defaultUri = new Uri(config('app.url'));
try {
    $domainInfo = (new App\Http\Middleware\ResolveCustomSubdomain())->splitHost($currentHost);
} catch (RuntimeException $e) {
    $domainInfo = null;
}
$storeSubDomain = !empty($domainInfo) && $domainInfo->getService() === 'store' ?
    $currentHost : 'store' . $defaultUri->getHost();*/
//use Illuminate\Support\Facades\Validator;

//use Carbon\Carbon;

Route::group(['namespace' => 'Dorcas\ModulesSettings\Http\Controllers', 'prefix' => 'mse', 'middleware' => ['web','auth']], function() {
    Route::get('/settings-main', 'ModulesSettingsController@index')->name('settings-main');

    Route::get('/settings-business', 'ModulesSettingsController@business_index')->name('settings-business');
    Route::post('/settings-business', 'ModulesSettingsController@business_post');

    Route::get('/settings-personal', 'ModulesSettingsController@personal_index')->name('settings-personal');
    Route::post('/settings-personal', 'ModulesSettingsController@personal_post');

    Route::get('/settings-security', 'ModulesSettingsController@security_index')->name('settings-security');
    Route::post('/settings-security', 'ModulesSettingsController@security_post');

    Route::get('/settings-customization', 'ModulesSettingsController@customization_index')->name('settings-customization');
    Route::post('/settings-customization', 'ModulesSettingsController@customization_post');

    Route::get('/settings-billing', 'ModulesSettingsController@billing_index')->name('settings-billing');
    Route::post('/settings-billing', 'ModulesSettingsController@billing_post');
    Route::post('/settings-billing-coupon', 'ModulesSettingsController@billing_coupon')->name('settings-billing-coupon');

    Route::get('/settings-banking', 'ModulesSettingsController@banking_index')->name('settings-banking');
    Route::post('/settings-banking', 'ModulesSettingsController@banking_post');

    Route::get('/settings-access-grants', 'ModulesSettingsController@access_grants_index')->name('settings-access-grants');
    Route::post('/settings-access-grants', 'ModulesSettingsController@access_grants_post');
    Route::get('/settings-access-grants-search', 'ModulesSettingsController@access_grants_search')->name('settings-access-grants-search');
    Route::delete('/settings-access-grants/{id}', 'ModulesSettingsController@access_grants_delete');

    Route::get('/settings-subscription', 'ModulesSettingsController@subscription')->name('settings-subscription');
    Route::post('/settings-subscription-coupon', 'ModulesSettingsController@subscription_coupon');
    Route::post('/settings-subscription-switch', 'ModulesSettingsController@subscription_switch');


    Route::post('/settings-marketplace', 'ModulesSettingsController@marketplace_settings');


});


//Route::post('/settings', 'Settings@update');

/*Route::group(['middleware' => ['auth']], function () {
    Route::get('/plans', 'UpgradePlan@index')->name('plans');
    Route::get('/subscription', 'Subscription@index')->name('subscription');
    Route::post('/subscription', 'Subscription@post');
});


Route::group(['middleware' => ['auth'], 'namespace' => 'Ajax', 'prefix' => 'xhr'], function () {
	Route::post('/settings', 'Settings@update');
});



$request = app()->make('request');
$currentHost = $request->header('host');
$defaultUri = new Uri(config('app.url'));
try {
    $domainInfo = (new App\Http\Middleware\ResolveCustomSubdomain())->splitHost($currentHost);
} catch (RuntimeException $e) {
    $domainInfo = null;
}
$storeSubDomain = !empty($domainInfo) && $domainInfo->getService() === 'store' ?
    $currentHost : 'store' . $defaultUri->getHost();

Route::prefix('store')->group(function () {
    Route::get('/', 'WebStore\RedirectRoute@index');
    Route::get('/categories/{id?}', 'WebStore\RedirectRoute@index');
    Route::get('/products/{id?}', 'WebStore\RedirectRoute@index');
    Route::get('/cart', 'WebStore\RedirectRoute@index');
});

Route::domain($storeSubDomain)->namespace('WebStore')->middleware(['web_store'])->group(function () {
    Route::get('/', 'Home@index')->name('webstore');
    Route::get('/categories', 'Home@categories')->name('webstore.categories');
    Route::get('/categories/{id}', 'Home@index')->name('webstore.categories.single');
    Route::get('/products', 'Home@products')->name('webstore.products');
    Route::get('/products/{id}', 'Home@productDetails')->name('webstore.products.details');
    Route::get('/cart', 'Cart@index')->name('webstore.cart');
    Route::get('/product-quick-view/{id}', 'Home@quickView')->name('webstore.quick-view');
    Route::delete('/xhr/cart', 'Cart@removeFromCartXhr');
    Route::post('/xhr/cart', 'Cart@addToCartXhr');
    Route::post('/xhr/cart/checkout', 'Cart@checkoutXhr');
    Route::put('/xhr/cart/update-quantities', 'Cart@updateCartQuantitiesXhr');
});*/

?>