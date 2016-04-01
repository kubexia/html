<?php
Route::group(['prefix' => 'admin','namespace' => 'Admin'],function(){
    Route::get('/', ['as' => 'admin.dashboard','uses' => 'Dashboard@index']);
    
    Route::group(['prefix' => 'demo', 'namespace' => 'Demo'],function(){
        Route::get('/forms', ['as' => 'admin.demo.forms','uses' => 'Forms@index']);
        Route::get('/forms/single', ['as' => 'admin.demo.forms.single','uses' => 'Forms@single']);
        Route::get('/forms/filters', ['as' => 'admin.demo.forms.filters','uses' => 'Forms@filters']);
        Route::get('/forms/multiple', ['as' => 'admin.demo.forms.multiple','uses' => 'Forms@multiple']);
        
        Route::get('/tables', ['as' => 'admin.demo.tables','uses' => 'Tables@index']);
    });
    
    Route::group(['prefix' => 'users', 'namespace' => 'Users'],function(){
        Route::get('', ['as' => 'admin.users','uses' => 'Users@index']);
        
        Route::get('/create', ['as' => 'admin.users.create','uses' => 'Users@create']);
        Route::post('/store', ['as' => 'admin.users.store','uses' => 'Users@store']);
        
        Route::group(['prefix' => '{user}', 'where' => ['user' => '([0-9]+)']],function(){
            Route::get('', ['as' => 'admin.users.edit','uses' => 'Users@edit']);
            Route::put('/update', ['as' => 'admin.users.update','uses' => 'Users@update']);
            Route::delete('/delete', ['as' => 'admin.users.destroy','uses' => 'Users@destroy']);
        });
        
        // ROLES //
        Route::group(['prefix' => 'roles'],function(){
            Route::get('', ['as' => 'admin.users.roles','uses' => 'Roles@index']);

            Route::get('/create', ['as' => 'admin.users.roles.create','uses' => 'Roles@create']);
            Route::post('/store', ['as' => 'admin.users.roles.store','uses' => 'Roles@store']);

            Route::group(['prefix' => '{role}', 'where' => ['role' => '([0-9]+)']],function(){
                Route::get('', ['as' => 'admin.users.roles.edit','uses' => 'Roles@edit']);
                Route::put('/update', ['as' => 'admin.users.roles.update','uses' => 'Roles@update']);
                Route::delete('/delete', ['as' => 'admin.users.roles.destroy','uses' => 'Roles@destroy']);
            });
        });
        
        // RIGHTS //
        Route::group(['prefix' => 'rights', 'namespace' => 'Rights'],function(){
            Route::get('', ['as' => 'admin.users.rights','uses' => 'Rights@index']);

            Route::get('/create', ['as' => 'admin.users.rights.create','uses' => 'Rights@create']);
            Route::post('/store', ['as' => 'admin.users.rights.store','uses' => 'Rights@store']);

            Route::group(['prefix' => '{right}', 'where' => ['right' => '([0-9]+)']],function(){
                Route::get('', ['as' => 'admin.users.rights.edit','uses' => 'Rights@edit']);
                Route::put('/update', ['as' => 'admin.users.rights.update','uses' => 'Rights@update']);
                Route::delete('/delete', ['as' => 'admin.users.rights.destroy','uses' => 'Rights@destroy']);
            });
            
            // RIGHTS SECTIONS //
            Route::group(['prefix' => 'sections'],function(){
                Route::get('', ['as' => 'admin.users.rights.sections','uses' => 'Sections@index']);

                Route::get('/create', ['as' => 'admin.users.rights.sections.create','uses' => 'Sections@create']);
                Route::post('/store', ['as' => 'admin.users.rights.sections.store','uses' => 'Sections@store']);

                Route::group(['prefix' => '{section}', 'where' => ['section' => '([0-9]+)']],function(){
                    Route::get('', ['as' => 'admin.users.rights.sections.edit','uses' => 'Sections@edit']);
                    Route::put('/update', ['as' => 'admin.users.rights.sections.update','uses' => 'Sections@update']);
                    Route::delete('/delete', ['as' => 'admin.users.rights.sections.destroy','uses' => 'Sections@destroy']);
                });
            });
        });
    });
    
    Route::group(['prefix' => 'settings', 'namespace' => 'Settings'],function(){
        
        /** LANGUAGES **/
        Route::group(['prefix' => 'languages'],function(){
            Route::get('', ['as' => 'admin.settings.languages','uses' => 'Languages@index']);

            Route::get('/create', ['as' => 'admin.settings.languages.create','uses' => 'Languages@create']);
            Route::post('/store', ['as' => 'admin.settings.languages.store','uses' => 'Languages@store']);

            Route::group(['prefix' => '{language}', 'where' => ['language' => '([0-9]+)']],function(){
                Route::get('', ['as' => 'admin.settings.languages.edit','uses' => 'Languages@edit']);
                Route::put('/update', ['as' => 'admin.settings.languages.update','uses' => 'Languages@update']);
                Route::delete('/delete', ['as' => 'admin.settings.languages.destroy','uses' => 'Languages@destroy']);
            });
        });
        
        /** TRANSLATIONS **/
        Route::group(['prefix' => 'translations'],function(){
            Route::get('', ['as' => 'admin.settings.translations','uses' => 'Translations@index']);

            Route::get('/create', ['as' => 'admin.settings.translations.create','uses' => 'Translations@create']);
            Route::post('/store', ['as' => 'admin.settings.translations.store','uses' => 'Translations@store']);

            Route::group(['prefix' => '{translation}', 'where' => ['translation' => '([0-9]+)']],function(){
                Route::get('', ['as' => 'admin.settings.translations.edit','uses' => 'Translations@edit']);
                Route::put('/update', ['as' => 'admin.settings.translations.update','uses' => 'Translations@update']);
                Route::delete('/delete', ['as' => 'admin.settings.translations.destroy','uses' => 'Translations@destroy']);
            });
        });
        
        /** COUNTRIES **/
        Route::group(['prefix' => 'countries'],function(){
            Route::get('', ['as' => 'admin.settings.countries','uses' => 'Countries@index']);
            
            Route::get('/create', ['as' => 'admin.settings.countries.create','uses' => 'Countries@create']);
            Route::post('/store', ['as' => 'admin.settings.countries.store','uses' => 'Countries@store']);

            Route::group(['prefix' => '{country}', 'where' => ['country' => '([0-9]+)']],function(){
                Route::get('', ['as' => 'admin.settings.countries.edit','uses' => 'Countries@edit']);
                Route::put('/update', ['as' => 'admin.settings.countries.update','uses' => 'Countries@update']);
                Route::delete('/delete', ['as' => 'admin.settings.countries.destroy','uses' => 'Countries@destroy']);
            });
        });
    });
    
   include __DIR__.'/modules/shop.php';
});
