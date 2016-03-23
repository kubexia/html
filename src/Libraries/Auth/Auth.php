<?php
namespace App\Libraries\Auth;

use App\Models\User\User;
use App\Models\User\Session as UserSession;

class Auth{
    
    protected static $instance = NULL;
    
    protected $tokenName = 'login_token';
    
    protected static $user = NULL;
    
    /**
     * 
     * @return \App\Libraries\Users\Auth
     */
    static public function getInstance(){
        if(is_null(static::$instance)){
            static::$instance = new Auth();
        }
        
        return static::$instance;
    }
    
    public function __construct(){
        
    }
    
    public function setSession(User $user,$clearPrevious=FALSE){
        if(\Session::get($this->tokenName)){
            return FALSE;
        }
        
        $token = $this->generateToken($user);
        
        if($clearPrevious){
            $user->sessions()->delete();
            $this->forgetSession();
        }
        
        \Session::set($this->tokenName, $token);
        \Session::save();
        
        $user->sessions()->save(new UserSession(array(
            'ip_address' => request()->getClientIp(),
            'token' => $token,
        )));
        
        return array(
            'token_value' => $token,
            'token_name' => $this->tokenName
        );
    }
    
    public function getSession(){
        $token = \Session::get($this->tokenName);
        if(!$token){
            return FALSE;
        }
        
        if(!is_null(static::$user)){
            return static::$user;
        }
        
        static::$user = User::select('users.*')->join('users_sessions',function($join){
            $join->on('users_sessions.user_id','=','users.id');
        })
        ->where('users_sessions.token','=',$token)->first();
        
        if(!static::$user){
            $this->forgetSession();
            return FALSE;
        }
        
        return static::$user;
    }
    
    public function clearSession($clearAll=FALSE){
        $user = $this->getSession();
        if(!$user){
            return $this->forgetSession();
        }
        
        if($clearAll){
            $user->sessions()->delete();
        }
        else{
            $token = \Session::get($this->tokenName);
            UserSession::where('token',$token)->delete();
        }
        
        $this->forgetSession();
        
        return TRUE;
    }
    
    protected function forgetSession(){
        \Session::forget($this->tokenName);
        \Session::save();
    }
    
    protected function generateToken(User $user){
        return md5(md5($user->password) . $user->id . $user->email. $user->username . microtime() . uniqid(rand(), TRUE));
    }
    
}
