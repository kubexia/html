<?php namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Role extends Model{
    
    protected $table = 'users_roles';
    
    public $timestamps = true;
    
    protected $fillable = ['name','slug'];
    
    static public function getRolesSelectBox(){
        $array = [];
        foreach(parent::query()->orderBy('rank','ASC')->get() as $row){
            $array[] = [
                'value' => $row->id,
                'label' => $row->name
            ];
        }
        
        return $array;
    }
    
    static public function getDefault($field = NULL){
        $role = parent::query()->where('is_default',1)->first();
        if(is_null($field)){
            return ($role ? $role : NULL);
        }
        return ($role ? $role->{$field} : NULL);
    }
}