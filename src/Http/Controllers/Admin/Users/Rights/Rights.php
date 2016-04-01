<?php namespace App\Http\Controllers\Admin\Users\Rights;

use App\Http\Base\Admin as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Table;
use App\Libraries\Filters;
use App\Libraries\HTML\Form;

use App\Models\User\Role;
use App\Models\User\Right\Right;
use App\Models\User\Right\Section;

class Rights extends BaseController {
    
    protected $topmenu;
    
    public function __construct(){
        parent::__construct();
        
        $this->topmenu = [
            ['title' => 'New Right', 'url' => route('admin.users.rights.create'), 'icon' => 'fa-plus'],
            ['title' => 'Rights', 'url' => route('admin.users.rights'), 'icon' => 'fa-key'],
            ['title' => 'Sections', 'url' => route('admin.users.rights.sections'), 'icon' => 'fa-sitemap'],
            ['icon' => 'fa-gears','submenu' => [
                ['title' => 'Manage Users', 'url' => route('admin.users')],
                ['title' => 'Manage Roles', 'url' => route('admin.users.roles')],
            ]],
        ];
    }

    public function index(Request $request){
        $filters = new Filters($request);
        
        $perPage = 20;
        $search  = Right::query();
        $search->orderBy('name','ASC');
        
        $results = $search->paginate($perPage);
        $results->appends($request->all());
        
        $table = (new Table())
            ->addOption('topmenu',$this->topmenu)
            ->addOption('pageTitle', 'User Rights')
            ->addOption('totalResults',$results->total())
            ->addResults($results);
        
        $table->addColumns([
            'name' => ['name' => 'Name','route' => 'route_edit'],
            'slug' => ['name' => 'Slug'],
            'section' => ['name' => 'Section', 
                'callback' => function($item){
                    $section = $item->section;
                    return ($section ? $section->name : NULL);
                },
                'route' => function($item){
                    $section = $item->section;
                    return ($section ? route('admin.users.rights.sections.edit',['section' => $section->id]) : NULL);
                }
            ],
        ]);
        
        $table->setCallback('route_edit',function($item){
            return route('admin.users.rights.edit',['right' => $item->id]);
        });
        
        $table->addRoute('destroy',[
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.'?';
            },
            'url' => function($item){
                return route('admin.users.rights.destroy',['right' => $item->id]);
            },
            'returnTo' => function(){
                return route('admin.users.rights');
            }
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $table
        ]);
    }
    
    public function create(){
        $form = (new Form('POST',route('admin.users.rights.store'),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter right name'])
            ->addElement('text','slug',['label' => 'Slug','required' => true],['placeholder' => 'enter slug or leave empty for auto-generation'])
            ->addElement('select','section',['label' => 'Section', 'options' => Section::getSectionsSelectBox()],['placeholder' => 'choose section or type new to create','tags' => true]);
        
        $form->addOption('pageTitle','New Right');
        $form->addOption('topmenu',$this->topmenu);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function store(Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required'
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('name')){
            if(Right::where('name',$request->get('name'))->first()){
                $errors->add('name', "This name is already taken");
            }
        }
        
        if(!$errors->has('slug')){
            if(Right::where('slug',($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_')))->first()){
                $errors->add('slug', "This slug is already taken");
            }
        }
        
        if(count($errors->all()) === 0){
            $section = ($request->has('section') ? Section::find($request->get('section')) : NULL);
            
            $right = new Right();
            $right->name = $request->get('name');
            $right->slug = ($request->has('slug') ? $request->get('slug') : (!is_null($section) ? $section->slug.'_' : '').str_slug($request->get('name'),'_'));
            if(!is_null($section)){
                $right->section()->associate($section);
            }
            $right->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_created',['attribute' => 'Right']),
                'delay' => 300,
                'redirect_to' => route('admin.users.rights.edit',['right' => $right->id])
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function edit(Right $right){
        $form = (new Form('PUT',route('admin.users.rights.update',['right' => $right->id]),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter right name','value' => $right->name])
            ->addElement('text','slug',['label' => 'Slug','required' => true],['placeholder' => 'enter slug or leave empty for auto-generation','value' => $right->slug])
            ->addElement('select','section',['label' => 'Section', 'options' => Section::getSectionsSelectBox()],['placeholder' => 'choose section or type new to create','tags' => true, 'selected' => $right->section_id])
            ;
        
        $form->addOption('pageTitle','Edit Right');
        $form->addOption('topmenu',$this->topmenu);
        
        $form->addRoute('destroy',[
            'item' => $right,
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.' right?';
            },
            'url' => function($item){
                return route('admin.users.rights.destroy',['right' => $item->id]);
            },
            'returnTo' => function($item){
                return route('admin.users.rights');
            },
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function update(Right $right, Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required',
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('name')){
            if(Right::where('name',$request->get('name'))->where('id','!=',$right->id)->first()){
                $errors->add('name', "This name is already taken");
            }
        }
        
        if(!$errors->has('slug')){
            if(Right::where('slug',($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_')))->where('id','!=',$right->id)->first()){
                $errors->add('slug', "This slug is already taken");
            }
        }
        
        if(count($errors->all()) === 0){
            $section = ($request->has('section') ? Section::find($request->get('section')) : NULL);
            
            $right->name = $request->get('name');
            $right->slug = ($request->has('slug') ? $request->get('slug') : (!is_null($section) ? $section->slug.'_' : '').str_slug($request->get('name'),'_'));
            if(!is_null($section)){
                $right->section()->associate($section);
            }
            $right->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_updated',['attribute' => 'Right']),
            ]);
        }
        
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function destroy(Right $right){
        $right->delete();
        
        $this->response->setSuccess(TRUE);
        return $this->response->toJson();
    }

}
