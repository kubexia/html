<?php namespace App\Http\Controllers\Admin\Users\Rights;

use App\Http\Base\Admin as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Table;
use App\Libraries\Filters;
use App\Libraries\HTML\Form;

use App\Models\User\Right\Section;

class Sections extends BaseController {
    
    protected $topmenu;
    
    public function __construct(){
        parent::__construct();
        
         $this->topmenu = [
            ['title' => 'New Section', 'url' => route('admin.users.rights.sections.create'), 'icon' => 'fa-plus'],
            ['title' => 'Sections', 'url' => route('admin.users.rights.sections'), 'icon' => 'fa-list'],
            ['title' => 'Rights', 'url' => route('admin.users.rights'), 'icon' => 'fa-key'],
            ['icon' => 'fa-gears','submenu' => [
                ['title' => 'Manage Users', 'url' => route('admin.users')],
                ['title' => 'Manage Roles', 'url' => route('admin.users.roles')],
            ]],
        ];
    }

    public function index(Request $request){
        $filters = new Filters($request);
        
        $perPage = 20;
        $search  = Section::query();
        $search->orderBy('name','ASC');
        
        $results = $search->paginate($perPage);
        $results->appends($request->all());
        
        $table = (new Table())
            ->addOption('topmenu',$this->topmenu)
            ->addOption('pageTitle', 'User Rights Sections')
            ->addOption('totalResults',$results->total())
            ->addResults($results);
        
        $table->addColumns([
            'name' => ['name' => 'Name','route' => 'route_edit'],
            'slug' => ['name' => 'Slug'],
        ]);
        
        $table->setCallback('route_edit',function($item){
            return route('admin.users.rights.sections.edit',['right' => $item->id]);
        });
        
        $table->addRoute('destroy',[
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.'?';
            },
            'url' => function($item){
                return route('admin.users.rights.sections.destroy',['right' => $item->id]);
            },
            'returnTo' => function(){
                return route('admin.users.rights.sections');
            }
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $table
        ]);
    }
    
    public function create(){
        $form = (new Form('POST',route('admin.users.rights.sections.store'),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter section name'])
            ->addElement('text','slug',['label' => 'Slug','required' => true],['placeholder' => 'enter slug or leave empty for auto-generation'])
            ->addElement('select','section',['label' => 'Parent', 'options' => Section::getSectionsSelectBox()],['placeholder' => 'choose parent section']);
        
        $form->addOption('pageTitle','New Section');
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
            if(Section::where('name',$request->get('name'))->first()){
                $errors->add('name', "This name is already taken");
            }
        }
        
        if(!$errors->has('slug')){
            if(Section::where('slug',($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_')))->first()){
                $errors->add('slug', "This slug is already taken");
            }
        }
        
        if(count($errors->all()) === 0){
            $section = new Section();
            $section->name = $request->get('name');
            $section->slug = ($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_'));
            $section->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_created',['attribute' => 'Section']),
                'delay' => 300,
                'redirect_to' => route('admin.users.rights.sections.edit',['right' => $section->id])
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function edit(Section $section){
        $form = (new Form('PUT',route('admin.users.rights.sections.update',['right' => $section->id]),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter section name','value' => $section->name])
            ->addElement('text','slug',['label' => 'Slug','required' => true],['placeholder' => 'enter slug or leave empty for auto-generation', 'value' => $section->slug])
            ->addElement('select','section',['label' => 'Parent', 'options' => Section::getSectionsSelectBox()],['placeholder' => 'choose parent section', 'selected' => $section->parent_id])
            ;
        
        $form->addOption('pageTitle','Edit Section');
        $form->addOption('topmenu',$this->topmenu);
        
        $form->addRoute('destroy',[
            'item' => $section,
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.' right?';
            },
            'url' => function($item){
                return route('admin.users.rights.sections.destroy',['section' => $item->id]);
            },
            'returnTo' => function($item){
                return route('admin.users.rights.sections');
            },
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'users',
            'content' => $form
        ]);
    }
    
    public function update(Section $section, Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required',
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('name')){
            if(Section::where('name',$request->get('name'))->where('id','!=',$section->id)->first()){
                $errors->add('name', "This name is already taken");
            }
        }
        
        if(!$errors->has('slug')){
            if(Section::where('slug',($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_')))->where('id','!=',$section->id)->first()){
                $errors->add('slug', "This slug is already taken");
            }
        }
        
        if(count($errors->all()) === 0){
            $section->name = $request->get('name');
            $section->slug = ($request->has('slug') ? $request->get('slug') : str_slug($request->get('name'),'_'));
            $section->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_updated',['attribute' => 'Section']),
            ]);
        }
        
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function destroy(Section $section){
        $section->delete();
        
        $this->response->setSuccess(TRUE);
        return $this->response->toJson();
    }

}
