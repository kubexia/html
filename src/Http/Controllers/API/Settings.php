<?php namespace App\Http\Controllers\API;

use App\Http\Base\API as BaseController;

class Settings extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        return $this->response->toJson();
    }
    
}
