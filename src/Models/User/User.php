<?php namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class User extends Model{
    
    protected $table = 'users';
    
    public $timestamps = true;
    
    protected $fillable = ['email','username','password','status','first_name','last_name'];
    
    /** RELATIONS **/
    
    public function roles(){
        return $this->belongsToMany('App\Models\User\Role', 'users_have_roles', 'user_id','role_id');
    }
    
    public function rights(){
        return $this->belongsToMany('App\Models\User\Right\Right', 'users_have_rights', 'user_id','right_id');
    }
    
    public function language(){
        return $this->belongsTo('App\Models\Settings\Language','language_id','id');
    }
    
    public function sessions(){
        return $this->hasMany('App\Models\User\Session','user_id','id');
    }
    
    /** SETTERS **/
    
    public function setPasswordAttribute($value){
        $this->attributes['password'] = static::hashPassword($value);
    }
    
    /** OTHER METHODS **/
    
    static public function hashPassword($string){
        return md5($string);
    }
    
    static public function authenticate($userOrEmail,$password){
        return parent::query()->whereRaw('(username = ? OR email = ?) AND password = ?',[$userOrEmail,$userOrEmail,  static::hashPassword($password)])->first();
    }
    
    static public function getStatusesSelectBox(){
        return [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'pending', 'label' => 'Pending'],
            ['value' => 'suspended', 'label' => 'Suspended'],
        ];
    }
    
    public function getRolesIds(){
        $array = [];
        foreach($this->roles as $row){
            $array[] = $row->id;
        }
        
        return $array;
    }
    
    public function getRolesSlugs(){
        $array = [];
        foreach($this->roles as $row){
            $array[] = $row->slug;
        }
        
        return $array;
    }
    
    public function hasRole(){
        foreach(func_get_args() as $role){
            if(in_array($role,$this->getRolesSlugs())){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function fullName(){
        return $this->first_name.' '.$this->last_name;
    }
    
    /***  API METHODS ***/
    public function apiData(){
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'name' => $this->fullName(),
            'status' => $this->status,
            'language' => $this->language->iso2,
        ];
    }
}