<?php namespace App\Http\Controllers\Admin\Users;

use App\Http\Base\Admin as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Table;
use App\Libraries\Filters;
use App\Libraries\HTML\Form;

use App\Models\User\User;
use App\Models\User\Role;
use App\Models\User\Right\Right;
use App\Models\Settings\Language;

class Users extends BaseController {
    
    protected $topmenu;
    
    public function __construct(){
        parent::__construct();
        
        $this->topmenu = [
            ['title' => trans('users_admin.menu.create_user'), 'url' => route('admin.users.create'), 'icon' => 'fa-user-plus'],
            ['title' => trans('users_admin.menu.list_users'), 'url' => route('admin.users'), 'icon' => 'fa-users'],
            ['icon' => 'fa-gears','submenu' => [
                ['title' => trans('users_admin.menu.list_roles'), 'url' => route('admin.users.roles')],
                ['title' => trans('users_admin.menu.list_rights'), 'url' => route('admin.users.rights')],
            ]],
        ];
    }

    public function index(Request $request){
        $filters = new Filters($request);
        
        $perPage = 20;
        $search  = User::query();
        
        if(!is_null($filters->get('term'))){
            $search->where(function($query) use ($filters){
                $query->orWhere('username','LIKE','%'.$filters->get('term').'%');
                $query->orWhere('email','LIKE','%'.$filters->get('term').'%');
                $query->orWhere('first_name','LIKE','%'.$filters->get('term').'%');
                $query->orWhere('last_name','LIKE','%'.$filters->get('term').'%');
            });
        }
        
        if(!is_null($filters->get('roles'))){
            $search->whereHas('roles', function($query) use ($filters){
                $query->whereIn('id',explode(',',$filters->get('roles')));
            });
        }
        
        if(!is_null($filters->get('rights'))){
            $search->whereHas('rights', function($query) use ($filters){
                $query->whereIn('id',explode(',',$filters->get('rights')));
            });
        }
        
        if(!is_null($filters->get('statuses'))){
            $search->whereIn('status',explode(',',$filters->get('statuses')));
        }
        
        if(!is_null($filters->get('languages'))){
            $search->whereIn('language_id',explode(',',$filters->get('languages')));
        }
        
        if(!is_null($filters->get('created_from')) && is_null($filters->get('created_to'))){
            $search->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') >= ?",[(new \DateTime($filters->get('created_from')))->format('Y-m-d')]);
        }
        elseif(is_null($filters->get('created_from')) && !is_null($filters->get('created_to'))){
            $search->where("DATE_FORMAT(created_at,'%Y-%m-%d') <= ?",[(new \DateTime($filters->get('created_to')))->format('Y-m-d')]);
        }
        elseif(!is_null($filters->get('created_from')) && !is_null($filters->get('created_to'))){
            $search->where(function($query) use ($filters){
                $query->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') >= ?",[(new \DateTime($filters->get('created_from')))->format('Y-m-d')]);
                $query->whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') <= ?",[(new \DateTime($filters->get('created_to')))->format('Y-m-d')]);
            });
        }
        
        if(!is_null($filters->get('sortby_field'))){
            $order = ($filters->get('sortby_order') ? $filters->get('sortby_order') : 'ASC');
            switch($filters->get('sortby_field')){
                case "created":
                    $search->orderBy('id',$order);
                    break;
                
                case "name":
                    $search->orderBy('first_name','ASC')->orderBy('last_name','ASC');
                    break;
            }
        }
        else{
            $search->orderBy('id','DESC');
        }
        
        $results = $search->paginate($perPage);
        $results->appends($request->all());
        
        $formFilters = (new Form('GET',route('admin.users')))
            ->addOption('cols', 2)
            ->addOption('totalFilters', $filters->count())
            ->addElement('text','term',['label' => trans('forms.fields.term.lb')],['placeholder' => trans('forms.fields.term.ph'),'value' => $filters->get('term')])
            ->addElement('select','roles',['label' => trans('users_admin.fields.role.lb_filter'),'options' => Role::getRolesSelectBox()],['placeholder' => trans('users_admin.fields.role.ph_filter'),'multiple' => true,'selected' => $filters->get('roles')])
            ->addElement('select','statuses',['label' => trans('forms.fields.status.filter.lb'),'options' => User::getStatusesSelectBox()],['placeholder' => trans('forms.fields.status.filter.ph'),'multiple' => true,'selected' => $filters->get('statuses')])
            ->addElement('datepickerBetween','created',['label' => trans('forms.fields.created_at.lb')],['placeholder_from' => trans('forms.fields.created_at.ph_from'),'placeholder_to' => trans('forms.fields.created_at.ph_to'),'value_from' => $filters->get('created_from'),'value_to' => $filters->get('created_to')])
            ->addElement('select','languages',['label' => trans('forms.fields.language.filter.lb'),'options' => Language::getSelectBox()],['placeholder' => trans('forms.fields.language.filter.ph'),'multiple' => true,'selected' => $filters->get('languages')])
            ->addElement('select','rights',['label' => trans('users_admin.fields.right.lb_filter'),'options' => Right::getRightsSelectBox()],['placeholder' => trans('users_admin.fields.right.ph_filter'),'multiple' => true,'selected' => $filters->get('rights')])
            ->addElement('selectSort','sortby',['label' => 'Order By','options' => [
                ['value' => 'created', 'label' => 'Date created'],
                ['value' => 'name', 'label' => 'Name'],
            ]],['placeholder_field' => 'choose sort field', 'placeholder_order' => 'order','selected_field' => $filters->get('sortby_field'),'selected_order' => $filters->get('sortby_order')]);
        
        $table = (new Table())
            ->addOption('showRowNumber',TRUE)
            ->addOption('topmenu',$this->topmenu)
            ->addOption('filters', $formFilters->make('filters'))
            ->addOption('pageTitle', trans('users_admin.users.table.title'))
            ->addOption('totalResults',$results->total())
            ->addResults($results);
        
        $table->addColumns([
            'created' => ['name' => trans('tables.columns.created_at'), 'callback' => function($item){
                return (new \DateTime($item->created_at))->format('d/m/Y');
            }],
            'email' => ['name' => trans('tables.columns.email'),'route' => 'route_edit'],
            'name' => ['name' => trans('tables.columns.full_name'), 'callback' => function($item){
                return $item->fullName();
            }],
            'status' => ['name' => 'Status']
        ]);
        
        $table->setCallback('route_edit',function($item){
            return route('admin.users.edit',['product' => $item->id]);
        });
        
        $table->addRoute('destroy',[
            'message' => function($item){
                return trans('tables.row.options.delete.confirm_p',['name' => $item->email]);
            },
            'url' => function($item){
                return route('admin.users.destroy',['user' => $item->id]);
            },
            'returnTo' => function(){
                return route('admin.users');
            }
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $table
        ]);
    }
    
    public function create(){
        $form = (new Form('POST',route('admin.users.store'),'horizontal'))
            ->addElement('select','roles',['label' => 'Roles','options' => Role::getRolesSelectBox(),'required' => true],['placeholder' => 'choose role','multiple' => true, 'selected' => Role::getDefault('id')])
            ->addElement('text','email',['label' => 'E-Mail Address','required' => true],['placeholder' => 'enter email'])
            ->addElement('break','break1')
            ->addElement('textGroup','name_group',['label' => 'Name'],[
                'items' => [
                    ['name' => 'first_name', 'placeholder' => 'enter first name'],
                    ['name' => 'last_name', 'placeholder' => 'enter last name']
                ]
            ])
            ->addElement('text','username',['label' => 'Username'],['placeholder' => 'enter username'])
            ->addElement('select','language',['label' => 'Language','options' => Language::getSelectBox()],['placeholder' => 'choose language','selected' => 1])
            ->addElement('break','break2')
            ->addElement('password','password',['label' => 'Password','generator' => true],['placeholder' => 'enter password or leave empty for autogeneration'])
            ;
        
        $form->addOption('pageTitle','New User');
        $form->addOption('topmenu',$this->topmenu);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function store(Request $request){
        $validator = \Validator::make($request->all(),[
            'role' => 'required',
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
            $user->username = ($request->has('username') ? $request->get('username') : NULL);
            $user->first_name = ($request->has('first_name') ? $request->get('first_name') : NULL);
            $user->last_name = ($request->has('last_name') ? $request->get('last_name') : NULL);
            $user->password = ($request->has('password') ? $request->get('password') : str_random(10));
            $user->roles()->sync(explode(',',$request->get('roles')));
            $user->language()->associate(Language::find($request->get('language')));
            $user->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_created',['attribute' => trans('messages.attributes.user')]),
                'delay' => 300,
                'redirect_to' => route('admin.users.edit',['user' => $user->id])
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function edit(User $user, Request $request){
        $form = (new Form('PUT',route('admin.users.update',['user' => $user->id]),'horizontal'))
            ->addElement('select','roles',['label' => 'Roles','options' => Role::getRolesSelectBox(),'required' => true],['placeholder' => 'choose role','selected' => join(',',$user->getRolesIds()),'multiple' => true])
            ->addElement('text','email',['label' => 'E-Mail Address','required' => true],['placeholder' => 'enter email','value' => $user->email])
            ->addElement('break','break1')
            ->addElement('textGroup','name_group',['label' => 'Name'],[
                'items' => [
                    ['name' => 'first_name', 'placeholder' => 'enter first name', 'value' => $user->first_name],
                    ['name' => 'last_name', 'placeholder' => 'enter last name', 'value' => $user->last_name]
                ]
            ])
            ->addElement('text','username',['label' => 'Username'],['placeholder' => 'enter username', 'value' => $user->username])
            ->addElement('select','language',['label' => 'Language','options' => Language::getSelectBox()],['placeholder' => 'choose language','selected' => $user->language_id])
            ->addElement('break','break2')
            ->addElement('password','password',['label' => 'Password','generator' => true],['placeholder' => 'enter password or leave empty for autogeneration'])
            ;
        
        $form->addOption('pageTitle','Edit User');
        $form->addOption('topmenu',$this->topmenu);
        
        $form->addRoute('destroy',[
            'item' => $user,
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->email.'?';
            },
            'url' => function($item){
                return route('admin.users.destroy',['user' => $item->id]);
            },
            'returnTo' => function($item){
                return route('admin.users');
            },
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function update(User $user, Request $request){
        $validator = \Validator::make($request->all(),[
            'roles' => 'required',
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
            $user->status = 'active';
            $user->email = $request->get('email');
            $user->username = ($request->has('username') ? $request->get('username') : NULL);
            $user->first_name = ($request->has('first_name') ? $request->get('first_name') : NULL);
            $user->last_name = ($request->has('last_name') ? $request->get('last_name') : NULL);
            $user->password = ($request->has('password') ? $request->get('password') : str_random(10));
            $user->roles()->sync(explode(',',$request->get('roles')));
            $user->language()->associate(Language::find($request->get('language')));
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
    
    public function destroy(User $user, Request $request){
        $user->delete();
        $this->response->setSuccess(TRUE);
        return $this->response->toJson();
    }

}
