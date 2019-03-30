<?php

/*Route::group(['namespace' => 'Dorcas\ModulesSettings\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('settings-main', 'ModulesSettingsController@index')->name('settings-main');
});*/


Route::group(['namespace' => 'Dorcas\ModulesSettings\Http\Controllers', 'middleware' => ['auth']], function() {
    Route::get('settings-main', 'ModulesSettingsController@index')->name('settings-main');

    Route::get('/settings-banking', 'ModulesSettingsController@banking_index')->name('settings-banking');
    Route::post('/settings-banking', 'ModulesSettingsController@banking_post');

    Route::get('/settings-business', 'ModulesSettingsController@business_index')->name('settings-business');
    Route::post('/settings/business', 'ModulesSettingsController@business_post');

    Route::get('/settings-billing', 'ModulesSettingsController@billing_index')->name('settings-billing');
    Route::post('/settings-billing', 'ModulesSettingsController@billing_post');

    Route::get('/settings-customization', 'ModulesSettingsController@customization_index')->name('settings-customization');
    Route::post('/settings/customization', 'ModulesSettingsController@customization_post');

    Route::get('/settings-personal', 'ModulesSettingsController@personal_index')->name('settings-personal');
    Route::post('/settings-personal', 'ModulesSettingsController@personal_post');

    Route::get('/settings-security', 'ModulesSettingsController@security_index')->name('settings-security');
    Route::post('/settings-security', 'ModulesSettingsController@security_post');

});

/**
 * Route Group for XHR: /xhr/...
 */
Route::group(['middleware' => ['auth'], 'namespace' => 'Ajax', 'prefix' => 'xhr'], function () {
	Route::post('/settings', 'Settings@update');
}

?>