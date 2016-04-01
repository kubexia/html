<?php namespace App\Http\Controllers\App\Users;

use App\Http\Base\App as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Form;

use App\Models\User\User;

class Users extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(Request $request){
        $perPage = 20;
        $search  = User::query();
        $search->orderBy('id','DESC');
        
        $results = $search->paginate($perPage);
        $results->appends($request->all());
        
        $table = (new \App\Libraries\HTML\Table())
            ->addOption('topmenu',[
                ['title' => 'New User', 'url' => route('app.users.create'), 'icon' => 'fa-user-plus'],
            ])
            ->addOption('pageTitle', 'Users')
            ->addOption('totalResults',$results->total())
            ->addResults($results);
        
        
        $table->addColumns([
            'name' => ['name' => 'Name'],
            'email' => ['name' => 'E-Mail', 
                'callback' => function($item){
                    return $item->email;
                },
                'route' => 'route_edit'
            ]
        ]);
            
        $table->setCallback('route_edit',function($item){
            return route('app.users.edit',['user' => $item->id]);
        });
                
        $table->addRoute('destroy',[
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->email.'?';
            },
            'url' => function($item){
                return route('app.users.destroy',['user' => $item->id]);
            },
            'returnTo' => function(){
                return route('app.users');
            }
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $table
        ]);
    }
    
    public function create(){
        $form = (new Form('POST',route('app.users.store'),'horizontal'))
            ->addElement('text','name',['label' => 'Name'],['placeholder' => 'enter name'])
            ->addElement('text','email',['label' => 'E-Mail Address','required' => true],['placeholder' => 'enter email'])
            ->addElement('text','username',['label' => 'Username'],['placeholder' => 'enter username'])
            ->addElement('password','password',['label' => 'Password'],['placeholder' => 'enter password or leave empty for autogeneration']);
        
        $form->addOption('pageTitle','New User');
        $form->addOption('topmenu',[
            ['title' => 'Users', 'url' => route('app.users'), 'icon' => 'fa-group'],
            ['title' => 'New User', 'url' => route('app.users.create'), 'icon' => 'fa-user-plus'],
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function store(Request $request){
        $validator = \Validator::make($request->all(),[
            'email' => 'required|email',
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('email')){
            if(User::where('email',$request->get('email'))->first()){
                $errors->add('email',trans('validation.email_taken'));
            }
        }
        
        if($request->has('username')){
            if(User::where('username',$request->get('username'))->first()){
                $errors->add('username',trans('validation.username_taken'));
            }
        }
        
        if(count($errors->all()) === 0){
            $user = new User();
            $user->status = 'active';
            $user->email = $request->get('email');
            $user->username = $request->get('username',NULL);
            $user->name = $request->get('name',NULL);
            $user->password = ($request->has('password') ? $request->get('password') : str_random(10));
            $user->role()->associate(\App\Models\User\Role::find(1));
            $user->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_created',['attribute' => trans('messages.attributes.user')]),
                'delay' => 300,
                'redirect_to' => route('app.users.edit',['user' => $user->id])
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function edit(User $user,Request $request){
        $form = (new Form('PUT',route('app.users.update',['user' => $user->id]),'horizontal'))
            ->addElement('text','name',['label' => 'Name'],['placeholder' => 'enter name','value' => $user->name])
            ->addElement('text','email',['label' => 'E-Mail Address','required' => true],['placeholder' => 'enter email','value' => $user->email])
            ->addElement('text','username',['label' => 'Username'],['placeholder' => 'enter username', 'value' => $user->username])
            ->addElement('password','password',['label' => 'Password'],['placeholder' => 'enter password or leave empty no changes','value' => '']);
        
        $form->addOption('pageTitle','Edit User');
        $form->addOption('topmenu',[
            ['title' => 'Users', 'url' => route('app.users'), 'icon' => 'fa-group'],
            ['title' => 'New User', 'url' => route('app.users.create'), 'icon' => 'fa-user-plus'],
        ]);
        
        $form->addRoute('destroy',[
            'item' => $user,
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->email.'?';
            },
            'url' => function($item){
                return route('app.users.destroy',['user' => $item->id]);
            },
            'returnTo' => function($item){
                return route('app.users');
            },
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function update(User $user,Request $request){
        $validator = \Validator::make($request->all(),[
            'email' => 'required|email',
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('email')){
            if(User::where('email',$request->get('email'))->where('id','!=',$user->id)->first()){
                $errors->add('email',trans('validation.email_taken'));
            }
        }
        
        if($request->has('username')){
            if(User::where('username',$request->get('username'))->where('id','!=',$user->id)->first()){
                $errors->add('username',trans('validation.username_taken'));
            }
        }
        
        if(count($errors->all()) === 0){
            $user->email = $request->get('email');
            $user->username = $request->get('username',NULL);
            $user->name = $request->get('name',NULL);
            if($request->has('password')){
                $user->password = ($request->has('password') ? $request->get('password') : str_random(10));
            }
            $user->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_updated',['attribute' => trans('messages.attributes.user')]),
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function destroy(User $user,Request $request){
        $user->delete();
        $this->response->setSuccess(TRUE);
        return $this->response->toJson();
    }
    
}
