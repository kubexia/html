<?php
namespace Kubexia\Http\Request;

use Illuminate\Http\Request;

class Filters{
    
    protected $request;
    
    protected $validator;
    
    protected $errors;
    
    protected $model;
    
    public function __construct(Request $request, array $attributes = []) {
        $this->request = $request;
        
        $this->validator = \Validator::make($request->all(),$attributes);
        
        $this->errors = $this->validator->messages();
    }
    
    public function setModel($model = NULL){
        $this->model = $model;
    }
    
    public function get($name){
        if($this->errors->has($name)){
            return NULL;
        }
        
        if($this->request->has($name)){
            return $this->request->get($name);
        }
        return NULL;
    }
    
    public function count(){
        $count = 0;
        foreach($this->request->all() as $key => $value){
            if(in_array($key,['page'])){
                continue;
            }
            
            if(strlen($value) > 0){
                $count++;
            }
        }
        return $count;
    }
    
}