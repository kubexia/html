<?php namespace App\Http\Controllers\Admin;

use App\Http\Base\Admin as BaseController;

class Dashboard extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        return redirect()->route('admin.users');
        return view('contents.admin.dashboard',[
            'activeMenu' => 'dashboard'
        ]);
    }
    
}
