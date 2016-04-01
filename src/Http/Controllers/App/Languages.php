<?php namespace App\Http\Controllers\App;

use App\Http\Base\App as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Form;

use App\Models\Language;

class Languages extends BaseController {
    
    public function __construct(){
        parent::__construct();
    }

    public function index(Request $request){
        $perPage = 20;
        $search  = Language::query();
        $search->orderBy('name','ASC');
        
        $results = $search->paginate($perPage);
        $results->appends($request->all());
        
        $table = (new \App\Libraries\HTML\Table())
            ->addOption('topmenu',[
                ['title' => 'New Language', 'url' => route('app.languages.create'), 'icon' => 'fa-plus'],
            ])
            ->addOption('pageTitle', 'Languages')
            ->addOption('totalResults',$results->total())
            ->addResults($results);
        
        $table->addColumns([
            'name' => ['name' => 'Name', 'route' => 'route_edit'],
            'iso2' => ['name' => 'ISO2']
        ]);
        
        $table->setCallback('route_edit',function($item){
            return route('app.languages.edit',['language' => $item->id]);
        });
        
        $table->addRoute('destroy',[
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.'?';
            },
            'url' => function($item){
                return route('app.languages.destroy',['language' => $item->id]);
            },
            'returnTo' => function(){
                return route('app.languages');
            }
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'languages',
            'content' => $table
        ]);
    }
    
    public function create(){
        $form = (new Form('POST',route('app.languages.store'),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter name'])
            ->addElement('text','iso2',['label' => 'ISO2','required' => true],['placeholder' => 'e.g. de,en,fr']);
        
        $form->addOption('pageTitle','New Language');
        $form->addOption('topmenu',[
            ['title' => 'Languages', 'url' => route('app.languages'), 'icon' => 'fa-language'],
            ['title' => 'New Language', 'url' => route('app.languages.create'), 'icon' => 'fa-plus'],
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'languages',
            'content' => $form
        ]);
    }
    
    public function store(Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required',
            'iso2' => 'required|max:2'
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('iso2')){
            if(Language::where('iso2',$request->get('iso2'))->first()){
                $errors->add('iso2','This iso2 already belongs to a language');
            }
        }
        
        if(count($errors->all()) === 0){
            $language = new Language();
            $language->name = $request->get('name');
            $language->iso2 = $request->get('iso2');
            $language->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_created',['attribute' => trans('messages.attributes.language')]),
                'delay' => 300,
                'redirect_to' => route('app.languages.edit',['language' => $language->id])
            ]);
        }
        
        $this->response->setErrors($errors);
        return $this->response->toJson();
    }
    
    public function edit(Language $language, Request $request){
        $form = (new Form('POST',route('app.languages.update',['language' => $language->id]),'horizontal'))
            ->addElement('text','name',['label' => 'Name','required' => true],['placeholder' => 'enter name','value' => $language->name])
            ->addElement('text','iso2',['label' => 'ISO2','required' => true],['placeholder' => 'e.g. de,en,fr','value' => $language->iso2]);
        
        $form->addOption('pageTitle','Edit Language');
        $form->addOption('topmenu',[
            ['title' => 'Languages', 'url' => route('app.languages'), 'icon' => 'fa-language'],
            ['title' => 'New Language', 'url' => route('app.languages.create'), 'icon' => 'fa-plus'],
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'languages',
            'content' => $form
        ]);
    }
    
    public function update(Language $language, Request $request){
        $validator = \Validator::make($request->all(),[
            'name' => 'required',
            'iso2' => 'required|max:2'
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('iso2')){
            if(Language::where('iso2',$request->get('iso2')->where('id','!=',$language->id))->first()){
                $errors->add('iso2','This iso2 already belongs to a language');
            }
        }
        
        if(count($errors->all()) === 0){
            $language->name = $request->get('name');
            $language->iso2 = $request->get('iso2');
            $language->save();
            
            $this->response->setSuccess(TRUE);
            
            $this->response->setMessage([
                'type' => 'success',
                'text' => trans('messages.item_created',['attribute' => trans('messages.attributes.language')]),
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
