<?php namespace App\Http\Controllers\Site;

use App\Http\Base\Site as BaseController;

class Homepage extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        if($this->user){
            return redirect()->route('app.dashboard');
        }
        else{
            return redirect()->route('site.login');
        }
    }
    
}
