<?php namespace App\Http\Controllers\Admin\Settings;

use App\Http\Base\Admin as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Table;
use App\Libraries\HTML\Form;
use App\Libraries\Filters;

use App\Models\Settings\Language;

class Translations extends BaseController {
    
    protected $topmenu;
    
    public function __construct(){
        parent::__construct();
        
        $this->topmenu = [
            ['title' => 'Import', 'url' => '#', 'icon' => 'fa-upload'],
            ['title' => 'Export', 'url' => '#', 'icon' => 'fa-download'],
        ];
    }
    
    public function index(Request $request){
        $results = [];
        $path = resource_path('lang/'.Language::getDefault()->iso2);
        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $filename => $object){
            if(in_array($object->getFileName(),['.','..'])){
                continue;
            }
            
            $array = include $filename;
            $arrayDot = array_dot($array);
            
            $results[] = [
                'filename' => str_replace('.php','',$object->getFileName()),
                'total' => count($arrayDot)
            ];
        }
        
        $collection = collect($results);
        $sorted = $collection->sortBy('filename');
        
        /*
        $path = resource_path('lang/en');
        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $filename => $object){
            if(in_array($object->getFileName(),['.','..'])){
                continue;
            }
            
            $array = include $filename;
            echo '<pre>';
            
            print_r($array);
            
            $arrayDot = array_dot($array);
            print_r($arrayDot);
            
            $dotToArray = array();
            foreach ($arrayDot as $key => $value) {
                array_set($dotToArray, $key, $value);
            }
            
            print_r($dotToArray);
            
            echo var_export($dotToArray, true);
            
            exit;
            $results[] = [
                'key' => str_replace('.php','',$object->getFileName()),
                'value' => NULL
            ];
        }
        */
        $table = (new Table())
            ->addOption('topmenu',$this->topmenu)
            ->addOption('pageTitle', 'Translation sections')
            ->addOption('totalResults',$collection->count())
            ->addResults($sorted->values()->all());
        
        $table->addColumns([
            'filename' => ['name' => 'Filename','route' => 'route_edit'],
            'total' => ['name' => 'Parameters']
        ]);
        
        $table->setCallback('route_edit',function($item){
            return '#';
        });
        
        return view('components.templates.page',[
            'activeMenu' => 'settings',
            'activeSubMenu' => 'translations',
            'content' => $table
        ]);
    }
}