<?php namespace App\Http\Base;

use App\Http\Base\Controller;

use App\Libraries\Auth\AuthAPI as Auth;

class API extends Controller {
    
    public $configs = [
        'section' => 'app',
        'theme' => 'default-angular'
    ];
    
    /**
     *
     * @var \App\Models\User\User
     */
    public $user;
    
    public function __construct(){
        $this->setConfigs();
        
        $token = request()->header('Token');
        if(request()->has('token')){
            $token = request()->get('token');
        }
        
        if($token){
            $this->user = ($token ? Auth::getInstance()->getSession($token) : FALSE);
            view()->share('user', $this->user);
        }
    }
}