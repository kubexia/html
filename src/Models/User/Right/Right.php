<?php namespace App\Models\User\Right;

use Illuminate\Database\Eloquent\Model;

class Right extends Model{
    
    protected $table = 'users_rights';
    
    public $timestamps = true;
    
    protected $fillable = ['name','slug'];
    
    public function section(){
        return $this->belongsTo('App\Models\User\Right\Section','section_id','id');
    }
    
    static public function getRightsSelectBox(){
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