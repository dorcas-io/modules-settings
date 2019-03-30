<?php

Route::group(['namespace' => 'Dorcas\ModulesSettings\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('settings-main', 'ModulesSettingsController@index')->name('settings-main');
});


?>