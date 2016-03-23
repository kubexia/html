<?php

namespace App\Http\Middleware;

use Closure;

use App\Libraries\Auth\AuthAPI;

class AuthenticateAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Token');
        if($request->has('token')){
            $token = $request->get('token');
        }
        
        if($token === NULL){
            return $this->errorInvalidToken('Token is missing');
        }
        else{
            $auth = AuthAPI::getInstance()->getSession($token);
            if(!$auth){
                return $this->errorInvalidToken('Invalid access token');
            }
        }
        
        return $next($request);
    }
    
    protected function errorInvalidToken($message){
        $response = new \App\Libraries\Http\Response\Response();
        $response->setErrors(['token' => $message]);
        return $response->toJson();
    }
    
}
