<?php namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Language extends Model{
    
    protected $table = 'settings_languages';
    
    public $timestamps = true;
    
    protected $fillable = ['name','iso2','is_default','is_fallback'];
    
    static protected $_cache = [];
    
    static public function getSelectBox(){
        $array = [];
        foreach(parent::query()->orderBy('name','ASC')->get() as $row){
            $array[] = [
                'value' => $row->id,
                'label' => $row->name
            ];
        }
        
        return $array;
    }
    
    static public function getDefault(){
        if(isset(static::$_cache['defaultTranslation'])){
            return static::$_cache['defaultTranslation'];
        }
        
        static::$_cache['defaultTranslation'] = parent::query()->where('is_default',1)->first();
        
        return static::$_cache['defaultTranslation'];
    }
    
    static public function getFallback(){
        return parent::query()->where('is_fallback',1)->first();
    }
    
}