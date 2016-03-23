<?php
namespace Kubexia\Http\Response;

class Response{
    
    protected $response = NULL;
    
    protected $errors = NULL;
    
    protected $message = NULL;
    
    protected $success = FALSE;
    
    protected $options = [
        'json' => 0
    ];
    
    public function setResponse($data = array()){
        if(is_null($this->response)){
            $this->response = array();
        }
        
        $this->response = (is_array($data) ? array_merge($this->response,(array) $data) : $data);
        return $this;
    }
    
    public function setMessage($data = NULL){
        if(is_array($this->message) || is_array($data)){
            $this->message = (is_array($this->message) ? array_merge($data,$this->message) : $data);
        }
        else{
            $this->message = $data;
        }
        return $this;
    }
    
    public function setErrors($fromValidator = NULL){
        if(is_object($fromValidator) && method_exists($fromValidator, 'all')){
            $this->errors = (count($fromValidator->all()) === 0 ? NULL : $fromValidator);
        }
        else{
            $this->errors = (is_array($fromValidator) && count($fromValidator) === 0 ? NULL : $fromValidator);
        }
        return $this;
    }
    
    public function setSuccess($success = FALSE){
        $this->success = $success;
        return $this;
    }
    
    /**
     * 
     * @param type constant $options JSON_NUMERIC_CHECK
     */
    public function setOptions($type,$options){
        $this->options[$type] = $options;
        return $this;
    }
    
    protected function getOptions($type){
        return (isset($this->options[$type]) ? $this->options[$type] : NULL);
    }
    
    public function toJson($status = 200, array $headers = array()){
        $response = [
            'response' => $this->response,
            'message' => $this->message,
            'errors' => $this->errors,
            'success' => $this->success
        ];
        
        if(starts_with(request()->root(),'http://localhost:8000')){
            $response['debug'] = \DB::getQueryLog();
        }
        
        return response()->json($response,$status,$headers,$this->getOptions('json'));
    }
    
}

