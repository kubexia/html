<?php namespace App\Http\Controllers\API\Users;

use App\Http\Base\API as BaseController;

class Profile extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->response->setResponse([
            'user' => $this->user->apiData()
        ]);
        $this->response->setSuccess(TRUE);
        return $this->response->toJson();
    }
    
    public function updateProfile(){
        return $this->response->toJson();
    }
    
    public function changePassword(){
        //old password, new password
        return $this->response->toJson();
    }
    
}
