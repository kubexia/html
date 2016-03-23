<?php namespace App\Http\Base;

use App\Http\Base\Controller;

use App\Libraries\Auth\Auth;

class Admin extends Controller {
    
    public $configs = [
        'section' => 'admin',
        'theme' => 'default'
    ];
    
    /**
     *
     * @var \App\Models\User\User
     */
    public $user;
    
    public function __construct(){
        $this->setConfigs();
        
        $this->user = Auth::getInstance()->getSession();
        
        if(!$this->user){
            return abort(404);
        }
        
        if(!$this->user->hasRole('admin')){
            return abort(404);
        }
        
        view()->share('user', $this->user);
    }
}