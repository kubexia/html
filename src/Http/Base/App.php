<?php namespace App\Http\Base;

use App\Http\Base\Controller;

use App\Libraries\Auth\Auth;

class App extends Controller {
    
    public $configs = [
        'section' => 'app',
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
            return redirect()->route('site.login')->send();
        }
        
        view()->share('user', $this->user);
    }
}