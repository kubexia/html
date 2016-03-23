<?php namespace App\Models\Settings\Country;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model{
    
    protected $table = 'settings_countries_translations';
    
    protected $fillable = ['translation'];
    
    public function language(){
        return $this->belongsTo('App\Models\Settings\Language', 'language_id', 'id');
    }
    
    public function country(){
        return $this->belongsTo('App\Models\Settings\Country\Country', 'country_id', 'id');
    }
    
    
}