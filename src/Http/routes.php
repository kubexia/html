<?php
Route::group(['middleware' => ['web']], function () {
    require __DIR__.'/Routes/site.php';
    require __DIR__.'/Routes/app.php';
    
    /* ADMIN */
    require __DIR__.'/Routes/admin.php';
});

Route::group(['prefix' => 'api','namespace' => 'API','middleware' => ['api']], function () {
    require __DIR__.'/Routes/api.php';
});
