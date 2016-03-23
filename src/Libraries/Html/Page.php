<?php
namespace Kubexia\Libraries\Html;

class Page{
    
    protected $data = [];
    
    protected $callbacks = [];
    
    protected $routes = [];
    
    static protected $elementTypes = [];
    
    public function __construct(){
        
    }
    
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }
    
    public function __get($name) {
        return (isset($this->data[$name]) ? $this->data[$name] : NULL);
    }
    
    public function addOption($name,$value){
        if(!isset($this->data['options'])){
            $this->data['options'] = array();
        }
        $this->data['options'][$name] = $value;
        return $this;
    }
    
    public function getOption($name){
        return (isset($this->data['options'][$name]) ? $this->data['options'][$name] : NULL);
    }
    
    public function hasOption($name){
        return (isset($this->data['options'][$name]) ? TRUE : FALSE);
    }
    
    public function addPageTitle($title){
        $this->data['pageTitle'] = $title;
        return $this;
    }
    
    public function get($name){
        return (isset($this->data[$name]) ? $this->data[$name] : NULL);
    }
    
    public function setCallback($name,$callback){
        $this->callbacks[$name] = $callback;
        
        return $this;
    }
    
    public function getCallback($name, $args = []){
        if(!isset($this->callbacks[$name])){
            return NULL;
        }
        
        if(!is_callable($this->callbacks[$name])){
            return FALSE;
        }
        
        return call_user_func($this->callbacks[$name], $args);
    }
    
    public function hasCallback($name){
        return (isset($this->callbacks[$name]) ? TRUE : FALSE);
    }
    
    public function addRoute($name,$args = []){
        $this->routes[$name] = $args;
        return $this;
    }
    
    public function hasRoute($name,$item = NULL){
        if(is_null($item)){
            return (isset($this->routes[$name]) ? TRUE : FALSE);
        }
        
        return (isset($this->routes[$name][$item]) ? TRUE : FALSE);
    }
    
    public function getRoute($name, $item = NULL, $args = []){
        if(!isset($this->routes[$name])){
            return FALSE;
        }
        
        if(is_null($item)){
            return $this->routes[$name];
        }
        
        if(!isset($this->routes[$name][$item])){
            return FALSE;
        }
        
        if(is_callable($this->routes[$name][$item])){
            return call_user_func_array($this->routes[$name][$item],$args);
        }
        
        return $this->routes[$name][$item];
    }
    
    public function addElementType($type){
        static::$elementTypes[] = $type;
    }
    
    public function getElementTypes(){
        return array_unique(static::$elementTypes);
    }
    
    public function hasElementType(){
        $types = func_get_args();
        foreach($types as $type){
            if(in_array($type,$this->getElementTypes())){
                return TRUE;
            }
        }
        
        return FALSE;
    }
}