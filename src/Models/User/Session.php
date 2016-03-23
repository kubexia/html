<?php namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Session extends Model{
    
    protected $table = 'users_sessions';
    
    public $timestamps = true;
    
    protected $fillable = ['ip_address','token'];
    
}