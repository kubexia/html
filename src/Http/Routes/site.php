<?php
Route::group(['namespace' => 'Site'],function(){
    Route::get('/', ['as' => 'home','uses' => 'Homepage@index']);
    
    Route::group(['namespace' => 'Users'],function(){
        Route::get('/login', ['as' => 'site.login','uses' => 'Login@index']);
        Route::post('/login', ['as' => 'site.login','uses' => 'Login@normal']);
        Route::get('/logout', ['as' => 'site.logout','uses' => 'Logout@index']);
    });
});

