<?php
namespace Kubexia\Libraries\Html;

use Kubexia\Libraries\Html\Page;

class Table extends Page{
    
    protected $results = [];
    
    protected $columns = [];
    
    public function __construct(){
        parent::__construct();
        
        $this->addOption('pageType', 'table');
    }
    
    public function addResults($results = []){
        $this->results = $results;
        return $this;
    }
    
    public function getResults(){
        return $this->results;
    }
    
    public function addColumn($field,$name='', array $attr = []){
        if(isset($attr['callback'])){
            if(is_callable($attr['callback'])){
                $this->setCallback($field.'_anonymous_callback', $attr['callback']);
                $attr['callback'] = $field.'_anonymous_callback';
            }
        }
        
        if(isset($attr['route'])){
            if(is_callable($attr['route'])){
                $this->setCallback($field.'_anonymous_route', $attr['route']);
                $attr['route'] = $field.'_anonymous_route';
            }
        }
        
        $this->columns[$field] = [
            'name' => $name,
            'attr' => $attr
        ];
        return $this;
    }
    
    public function addColumns(array $array = []){
        foreach($array as $field => $attributes){
            $name = $attributes['name'];
            unset($attributes['name']);
            
            $this->addColumn($field, $name, $attributes);
        }
        return $this;
    }
    
    public function getColumns(){
        return $this->columns;
    }
    
    public function addColumnAfter($afterColumn,$name, array $array = []){
        $columns = [];
        foreach($this->columns as $field => $values){
            if($field === $afterColumn){
                $columns[$field] = $values;
                $columns[$name] = $array;
            }
            else{
                $columns[$field] = $values;
            }
        }
        $this->columns = $columns;
        
        return $this;
    }
    
    public function make(){
        return view('kubexia::templates.table',['table' => $this]);
    }
    
}