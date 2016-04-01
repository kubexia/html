<?php

namespace App\Http\Base;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    
    protected $configs = [];
    
    /**
     *
     * @var \App\Libraries\Response 
     */
    public $response;
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function setConfigs(){
        //set configs
        config($this->configs);
        
        //set themes folder
        $location = (isset($this->configs['location']) ? $this->configs['location'] : 'themes');
        
        view()->addLocation(public_path($location.'/'.$this->configs['section'].'/'.$this->configs['theme']));
        
        $this->response = new \App\Libraries\Http\Response\Response();
    }
}
