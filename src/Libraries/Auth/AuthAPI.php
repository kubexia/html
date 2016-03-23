<?php
namespace Kubexia\Libraries\Auth;

use App\Models\User\User;
use App\Models\User\Session as UserSession;

class AuthAPI{
    
    protected static $instance = NULL;
    
    protected static $user = NULL;
    
    /**
     * 
     * @return \App\Libraries\Users\Auth
     */
    static public function getInstance(){
        if(is_null(static::$instance)){
            static::$instance = new AuthAPI();
        }
        
        return static::$instance;
    }
    
    public function __construct(){
        
    }
    
    public function setSession(User $user,$clearPrevious=FALSE){
        $token = $this->generateToken($user);
        
        if($clearPrevious){
            $user->sessions()->delete();
        }
        
        if(count($user->sessions) > 0){
            $user->sessions()->first()->save(new UserSession(array(
                'ip_address' => request()->getClientIp(),
            )));
            $session = $user->sessions()->first();
            $token = $session->token;
        }
        else{
            $user->sessions()->save(new UserSession(array(
                'ip_address' => request()->getClientIp(),
                'token' => $token,
            )));
        }
        
        return array(
            'token_value' => $token,
        );
    }
    
    public function getSession($token){
        if(is_null($token)){
            return false;
        }
        
        if(!is_null(static::$user)){
            return static::$user;
        }
        
        static::$user = User::select('users.*')->join('users_sessions',function($join){
            $join->on('users_sessions.user_id','=','users.id');
        })
        ->where('users_sessions.token','=',$token)->first();
        
        return static::$user;
    }
    
    public function clearSession($token=NULL,$clearAll=FALSE){
        $user = $this->getSession();
        if(!$user){
            return FALSE;
        }
        
        if($clearAll){
            $user->sessions()->delete();
        }
        else{
            UserSession::where('token',$token)->delete();
        }
        
        return TRUE;
    }
    
    protected function generateToken(User $user){
        return md5($user->username. $user->id . $user->email . microtime() . uniqid(rand(), TRUE));
    }
    
}
