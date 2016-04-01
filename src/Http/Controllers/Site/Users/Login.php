<?php namespace App\Http\Controllers\Site\Users;

use App\Http\Base\Site as BaseController;

use Illuminate\Http\Request;
use App\Models\User\User;

class Login extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(Request $request){
        if($this->user){
            return redirect()->route('dashboard');
        }
        
        return view('contents.site.users.login',[
            'redirectTo' => $request->get('redirect_to')
        ]);
    }
    
    public function normal(Request $request){
        $validator = \Validator::make($request->all(),[
            'userOrEmail' => 'required',
            'password' => 'required',
        ]);
        
        $errors = $validator->messages();
        
        $user = NULL;
        if(count($errors->all()) === 0){
            $user = User::authenticate($request->get('userOrEmail'),$request->get('password'));
            if(!$user){
                $errors->add('login_failed',  trans('auth.failed'));
            }
        }
        
        if(count($errors->all()) === 0 && !is_null($user)){
            switch($user->status){
                case 'suspended':
                    $errors->add('login_failed',  trans('auth.account.status.suspended'));
                    break;
                
                case 'pending':
                    $errors->add('login_failed',  trans('auth.account.status.pending'));
                    break;
                
                case 'active':
                    $redirectTo = NULL;
                        
                    if($request->has('redirect_to')){
                        $redirectTo = $request->get('redirect_to');
                    }
                    
                    \App\Libraries\Users\Auth::getInstance()->setSession($user,TRUE);
                    
                    $this->response->setResponse([
                        'redirect_to' => ($redirectTo ? $redirectTo : route('app.dashboard'))
                    ]);

                    $this->response->setSuccess(TRUE);
                    
                    break;
            }
        }
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
}
