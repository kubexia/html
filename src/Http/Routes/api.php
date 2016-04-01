<?php
Route::get('/settings', ['as' => 'api.settings','uses' => 'Settings@index','middleware' => 'authApi']);

Route::group(['prefix' => 'users', 'namespace' => 'Users'],function(){
    Route::post('/login', ['as' => 'api.users.login','uses' => 'Login@index']);
    Route::post('/register', ['as' => 'api.users.register','uses' => 'Register@index']);
    Route::post('/password-recovery', ['as' => 'api.users.password.recovery','uses' => 'PasswordRecovery@index']);
    
    Route::group(['prefix' => 'profile', 'middleware' => 'authApi'],function(){
        Route::get('', ['as' => 'api.users.profile','uses' => 'Profile@index']);
        Route::put('', ['as' => 'api.users.profile.update','uses' => 'Profile@updateProfile']);
        Route::put('/password', ['as' => 'api.users.profile.update.password','uses' => 'Profile@changePassword']);
    });
    
});

