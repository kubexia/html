<?php namespace App\Http\Controllers\API\Users;

use App\Http\Base\API as BaseController;

class Register extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        return $this->response->toJson();
    }
    
}
