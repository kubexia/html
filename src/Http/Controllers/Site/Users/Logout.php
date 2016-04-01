<?php namespace App\Http\Controllers\Site\Users;

use App\Http\Base\Site as BaseController;

class Logout extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        \App\Libraries\Users\Auth::getInstance()->clearSession();
        return redirect()->route('site.login');
    }
    
}
