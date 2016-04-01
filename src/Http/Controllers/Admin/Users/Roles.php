<?php namespace App\Http\Controllers\Admin\Users;

use App\Http\Base\Admin as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Table;
use App\Libraries\Filters;
use App\Libraries\HTML\Form;

use App\Models\User\Role;

class Roles extends BaseController {
    
    protected $topmenu;
    
    public function __construct(){
        parent::__construct();
        
        $this->topmenu = [
            ['title' => 'New Role', 'url' => route('admin.users.roles.create'), 'icon' => 'fa-plus'],
            ['title' => 'Roles', 'url' => route('admin.users.roles'), 'icon' => 'fa-star'],
            ['icon' => 'fa-gears','submenu' => [
                ['title' => 'Manage Users', 'url' => route('admin.users')],
                ['title' => 'Manage Rights', 'url' => route('admin.users.rights')],
            ]],
        ];
    }

    public function index(Request $request){
        $filters = new Filters($request);
        
        $perPage = 20;
        $search  = Role::query();
        $search->orderBy('rank','ASC');
        
        $results = $search->paginate($perPage);
        $results->appends($request->all());
        
        $table = (new Table())
            ->addOption('topmenu',$this->topmenu)
            ->addOption('pageTitle', 'User Roles')
            ->addOption('totalResults',$results->total())
            ->addResults($results);
        
        $table->addColumns([
            'rank' => ['name' => 'Rank', 'callback' => function($item){
                return (!is_null($item->rank) ? '<span class="badge"><i class="fa fa-star fa-fw"></i> '.$item->rank.'</span>' : '-');
            }],
            'name' => ['name' => 'Name','route' => 'route_edit'],
            'slug' => ['name' => 'Slug'],
            'is_default' => ['name' => 'Default?', 'callback' => function($item){
                return ($item->is_default > 0 ? '<i class="fa fa-check text-success"></i> Yes' : '<i class="fa fa-ban text-muted"></i> <span class="text-muted">No</span>');
            }]
        ]);
        
        $table->setCallback('route_edit',function($item){
            return route('admin.users.roles.edit',['role' => $item->id]);
        });
        
        $table->addRoute('destroy',[
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.'?';
            },
            'url' => function($item){
                return route('admin.users.roles.destroy',['role' => $item->id]);
            },
            'returnTo' => function(){
                return route('admin.users.roles');
            }
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $table
        ]);
    }
    
    public function create(){
        $roles = Role::getRolesSelectBox();
        $lastRole = end($roles);
        $form = (new Form('POST',route('admin.users.roles.store'),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter role name'])
            ->addElement('text','slug',['label' => 'Slug','required' => true],['placeholder' => 'enter slug or leave empty for auto-generation'])
            ->addElement('selectGroup','rank_group',['label' => 'Hirearchy'],[
                'items' => [
                    ['name' => 'position', 'placeholder' => 'choose position', 'selected' => 'after', 'options' => [
                        ['value' => 'before', 'label' => 'Before'],
                        ['value' => 'after', 'label' => 'After'],
                    ]],
                    ['name' => 'role', 'placeholder' => 'choose role', 'options' => $roles, 'selected' => $lastRole['value']]
                ]
            ])
            ->addElement('checkbox','is_default',['title' => 'Set default role'],['value' => 'yes'])
            ;
        
        $form->addOption('pageTitle','New Role');
        $form->addOption('topmenu',$this->topmenu);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function store(Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required',
            'position' => 'required',
            'role' => 'required'
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('name')){
            if(Role::where('name',$request->get('name'))->first()){
                $errors->add('name', "This name is already taken");
            }
        }
        
        if(!$errors->has('slug')){
            if(Role::where('slug',($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_')))->first()){
                $errors->add('slug', "This slug is already taken");
            }
        }
        
        if(count($errors->all()) === 0){
            $role = new Role();
            $role->name = $request->get('name');
            $role->slug = ($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_'));
            
            $rank = Role::find($request->get('role'));
            switch($request->get('position')){
                case "after":
                    $role->rank = $rank->rank+1;
                    Role::where('rank','>',$rank->rank)->increment('rank');
                    break;
                
                case "before":
                    $newRank = $rank->rank;
                    if($newRank === 0){
                        Role::where('rank','>=',1)->increment('rank');
                        $newRank = 1;
                    }
                    else{
                        Role::where('rank','>=',$rank->rank)->increment('rank');
                    }
                    $role->rank = $newRank;
                    break;
            }
            
            $role->is_default = ($request->has('is_default') ? 1 : NULL);
            $role->save();
            
            if($request->has('is_default')){
                Role::where('id','!=',$role->id)->update(['is_default' => NULL]);
            }
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_created',['attribute' => 'Role']),
                'delay' => 300,
                'redirect_to' => route('admin.users.roles.edit',['role' => $role->id])
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function edit(Role $role){
        $form = (new Form('PUT',route('admin.users.roles.update',['role' => $role->id]),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter role name','value' => $role->name])
            ->addElement('text','slug',['label' => 'Slug','required' => true],['placeholder' => 'enter slug or leave empty for auto-generation','value' => $role->slug])
            ->addElement('checkbox','is_default',['title' => 'Set default role'],['checked' => ((int) $role->is_default === 1 ? TRUE : FALSE),'value' => 'yes'])
            ;
        
        $form->addOption('pageTitle','Edit Role');
        $form->addOption('topmenu',$this->topmenu);
        
        $form->addRoute('destroy',[
            'item' => $role,
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.' role?';
            },
            'url' => function($item){
                return route('admin.users.roles.destroy',['role' => $item->id]);
            },
            'returnTo' => function($item){
                return route('admin.users.roles');
            },
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function update(Role $role, Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required',
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('name')){
            if(Role::where('name',$request->get('name'))->where('id','!=',$role->id)->first()){
                $errors->add('name', "This name is already taken");
            }
        }
        
        if(!$errors->has('slug')){
            if(Role::where('slug',($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_')))->where('id','!=',$role->id)->first()){
                $errors->add('slug', "This slug is already taken");
            }
        }
        
        if(count($errors->all()) === 0){
            $role->name = $request->get('name');
            $role->slug = ($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_'));
            $role->is_default = ($request->has('is_default') ? 1 : NULL);
            $role->save();
            
            if($request->has('is_default')){
                Role::where('id','!=',$role->id)->update(['is_default' => NULL]);
            }
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_updated',['attribute' => 'Role']),
            ]);
        }
        
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function destroy(Role $role){
        $rank = $role->rank;
        $role->delete();
        
        Role::where('rank','>',$rank)->decrement('rank');
        
        $this->response->setSuccess(TRUE);
        return $this->response->toJson();
    }

}
