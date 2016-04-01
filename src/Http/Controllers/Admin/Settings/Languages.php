<?php namespace App\Http\Controllers\Admin\Settings;

use App\Http\Base\Admin as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Table;
use App\Libraries\HTML\Form;

use App\Models\Settings\Language;

class Languages extends BaseController {
    
    protected $topmenu;
    
    public function __construct(){
        parent::__construct();
        
        $this->topmenu = [
            ['title' => 'New Language', 'url' => route('admin.settings.languages.create'), 'icon' => 'fa-plus'],
        ];
    }

    public function index(Request $request){
        $perPage = 20;
        $search  = Language::query();
        $search->orderBy('name','ASC');
        
        $results = $search->paginate($perPage);
        $results->appends($request->all());
        
        $table = (new Table())
            ->addOption('topmenu',$this->topmenu)
            ->addOption('pageTitle', 'Languages')
            ->addOption('totalResults',$results->total())
            ->addResults($results);
        
        $table->addColumns([
            'name' => ['name' => 'Name','route' => 'route_edit'],
            'iso2' => ['name' => 'ISO2'],
            'is_default' => ['name' => 'Default', 'callback' => function($item){
                return ($item->is_default ? '<span class="text-success"><i class="fa fa-check"></i> Yes</span>' : '<span class="text-muted"><i class="fa fa-ban"></i> No</span>');
            }],
            'is_fallback' => ['name' => 'Fallback', 'callback' => function($item){
                return ($item->is_fallback ? '<span class="text-success"><i class="fa fa-check"></i> Yes</span>' : '<span class="text-muted"><i class="fa fa-ban"></i> No</span>');
            }],
        ]);
        
        $table->setCallback('route_edit',function($item){
            return route('admin.settings.languages.edit',['language' => $item->id]);
        });
        
        $table->addRoute('destroy',[
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.'?';
            },
            'url' => function($item){
                return route('admin.settings.languages.destroy',['language' => $item->id]);
            },
            'returnTo' => function(){
                return route('admin.settings.languages');
            }
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'settings',
            'activeSubMenu' => 'languages',
            'content' => $table
        ]);
    }
    
    public function create(){
        $form = (new Form('POST',route('admin.settings.languages.store'),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter name'])
            ->addElement('text','iso2',['label' => 'ISO2','required' => true],['placeholder' => 'e.g. en,de,fr'])
            ->addElement('checkbox','is_default',['title' => 'Is default language'],['value' => 'yes'])
            ->addElement('checkbox','is_fallback',['title' => 'Is fallback language'],['value' => 'yes']);
        
        $form->addOption('pageTitle','New Language');
        $form->addOption('topmenu',$this->topmenu);
        
        return view('components.templates.page',[
            'activeMenu' => 'settings',
            'activeSubMenu' => 'languages',
            'content' => $form
        ]);
    }
    
    public function store(Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required',
            'iso2' => 'required'
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('name')){
            if(Language::where('name',$request->get('name'))->first()){
                $errors->add('name', 'This language name already exists');
            }
        }
        
        if($request->has('username')){
            if(Language::where('iso2',$request->get('iso2'))->first()){
                $errors->add('iso2','This iso2 already exists');
            }
        }
        
        if(count($errors->all()) === 0){
            $language = new Language();
            $language->name = $request->get('name');
            $language->iso2 = $request->get('iso2');
            
            $toUpdate = [];
            if($request->has('is_default')){
                $toUpdate['is_default'] = NULL;
                $language->is_default = 1;
            }
            
            if($request->has('is_fallback')){
                $toUpdate['is_fallback'] = NULL;
                $language->is_fallback = 1;
            }
            
            $language->save();
            if(count($toUpdate) > 0){
                Language::where('id','!=',$language->id)->update($toUpdate);
            }
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_created',['attribute' => 'Language']),
                'delay' => 300,
                'redirect_to' => route('admin.settings.languages.edit',['language' => $language->id])
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function edit(Language $language, Request $request){
        $form = (new Form('PUT',route('admin.settings.languages.update',['language' => $language->id]),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter name','value' => $language->name])
            ->addElement('text','iso2',['label' => 'ISO2','required' => true],['placeholder' => 'e.g. en,de,fr','value' => $language->iso2])
            ->addElement('checkbox','is_default',['title' => 'Is default language'],['value' => 'yes','checked' => ($language->is_default ? true : false)])
            ->addElement('checkbox','is_fallback',['title' => 'Is fallback language'],['value' => 'yes','checked' => ($language->is_fallback ? true : false)]);
        
        $form->addOption('pageTitle','Edit Language');
        $form->addOption('topmenu',$this->topmenu);
        
        $form->addRoute('destroy',[
            'item' => $language,
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.'?';
            },
            'url' => function($item){
                return route('admin.settings.languages.destroy',['language' => $item->id]);
            },
            'returnTo' => function($item){
                return route('admin.settings.languages');
            },
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'settings',
            'activeSubMenu' => 'languages',
            'content' => $form
        ]);
    }
    
    public function update(Language $language, Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required',
            'iso2' => 'required'
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('name')){
            if(Language::where('name',$request->get('name'))->where('id','!=',$language->id)->first()){
                $errors->add('name', 'This language name already exists');
            }
        }
        
        if($request->has('username')){
            if(Language::where('iso2',$request->get('iso2'))->where('id','!=',$language->id)->first()){
                $errors->add('iso2','This iso2 already exists');
            }
        }
        
        if(count($errors->all()) === 0){
            $language->name = $request->get('name');
            $language->iso2 = $request->get('iso2');
            
            $toUpdate = [];
            if($request->has('is_default')){
                $toUpdate['is_default'] = NULL;
                $language->is_default = 1;
            }
            
            if($request->has('is_fallback')){
                $toUpdate['is_fallback'] = NULL;
                $language->is_fallback = 1;
            }
            
            $language->save();
            if(count($toUpdate) > 0){
                Language::where('id','!=',$language->id)->update($toUpdate);
            }
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_updated',['attribute' => 'Language']),
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function destroy(Language $language, Request $request){
        $language->delete();
        $this->response->setSuccess(TRUE);
        return $this->response->toJson();
    }

}
