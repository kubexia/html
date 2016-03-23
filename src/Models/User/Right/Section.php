<?php namespace App\Models\User\Right;

use Illuminate\Database\Eloquent\Model;

class Section extends Model{
    
    protected $table = 'users_rights_sections';
    
    public $timestamps = true;
    
    protected $fillable = ['parent_id','name','slug'];
    
    static public function getSectionsSelectBox(){
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