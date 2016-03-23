<?php namespace App\Http\Base;

use App\Http\Base\Controller;

use App\Libraries\Auth\Auth;

class Site extends Controller {
    
    public $configs = [
        'section' => 'site',
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
        if($this->user){
            view()->share('user', $this->user);
        }
    }
}