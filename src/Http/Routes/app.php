<?php
Route::group(['namespace' => 'App'],function(){
    Route::get('/dashboard', ['as' => 'app.dashboard','uses' => 'Dashboard@index']);
});
