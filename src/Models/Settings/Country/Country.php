<?php namespace App\Models\Settings\Country;

use Illuminate\Database\Eloquent\Model;

class Country extends Model{
    
    protected $table = 'settings_countries';
    
    protected $fillable = ['country_code','phone_code','priority','eu_member'];
    
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
    
}