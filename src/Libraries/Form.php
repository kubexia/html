<?php
namespace Kubexia\HTML\Libraries;

use Kubexia\HTML\Libraries\Page;

class Form extends Page{
    
    public function __construct($method='POST',$action='#', $template = 'horizontal', $grid = NULL){
        parent::__construct();
        
        $this->addOption('pageType', 'form');
        
        if(!is_null($template)){
            $this->addOption('formTemplate', $template);
        }
        $this->addOption('formGrid', (is_null($grid) ? [2,10] : $grid));
        
        $this->setMethod($method);
        $this->setAction($action);
    }
    
    public function setMethod($method){
        $this->data['method'] = $method;
        return $this;
    }
    
    public function setAction($action){
        $this->data['action'] = $action;
        return $this;
    }
    
    public function addElement($type,$name,array $element=[],array $attr=[]){
        $element['name'] = $name;
        $element['type'] = $type;
        
        $this->addElementType($type);
        
        $this->data['form_elements'][$name] = [
            'element' => $element,
            'attributes' => $attr
        ];
        return $this;
    }
    
    public function addElements(array $array = []){
        foreach($array as $name => $element){
            $type = (isset($element['type']) ? $element['type'] : 'input');
            $attributes = (isset($element['attributes']) ? $element['attributes'] : []);
            $this->addElement($type,$name,$element,$attributes);
        }
        return $this;
    }
    
    public function getElements(){
        return (isset($this->data['form_elements']) ? $this->data['form_elements'] : []);
    }
    
    public function getElement($name){
        return (isset($this->data['form_elements'][$name]) ? $this->data['form_elements'][$name] : NULL);
    }
    
    public function getMethod(){
        return (!is_null($this->method) ? $this->method : 'POST');
    }
    
    public function getAction(){
        return (!is_null($this->action) ? $this->action : '#');
    }
    
    public function make($template=NULL){
        $template = (is_null($template) ? $this->getOption('formTemplate') : $template);
        
        return view('components.templates.forms.form_'.$template,['form' => $this]);
    }
    
}