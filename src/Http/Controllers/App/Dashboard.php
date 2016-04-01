<?php namespace App\Http\Controllers\App;

use App\Http\Base\App as BaseController;

class Dashboard extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        return view('contents.app.dashboard');
    }
    
}
