<?php namespace App\Http\Controllers\Admin\Settings;

use App\Http\Base\Admin as BaseController;

use Illuminate\Http\Request;
use App\Libraries\HTML\Table;
use App\Libraries\HTML\Form;
use App\Libraries\Filters;

use App\Models\Settings\Language;
use App\Models\Settings\Country\Country;
use App\Models\Settings\Country\Translation;

class Countries extends BaseController {
    
    protected $topmenu;
    
    public function __construct(){
        parent::__construct();
        
        $this->topmenu = [
            ['title' => 'New Country', 'url' => route('admin.settings.countries.create'), 'icon' => 'fa-plus'],
        ];
    }
    
    public function index(Request $request){
        $filters = new Filters($request);
        
        $perPage = 20;
        $search  = Translation::query();
        $search->selectRaw('settings_countries_translations.*, settings_countries.priority, settings_countries.country_code, settings_countries.phone_code, settings_countries.eu_member');
        $search->leftJoin('settings_countries','settings_countries.id','=','settings_countries_translations.country_id');
        
        if(!is_null($filters->get('term'))){
            $search->where(function($query) use ($filters){
                $query->orWhere('settings_countries_translations.translation','LIKE','%'.$filters->get('term').'%');
                $query->orWhere('settings_countries.phone_code','LIKE','%'.$filters->get('term').'%');
                $query->orWhere('settings_countries.country_code','LIKE','%'.$filters->get('term').'%');
            });
        }
        
        if(!is_null($filters->get('euMember'))){
            $search->where('settings_countries.eu_member','=',($filters->get('euMember') === 'yes' ? 1: 0));
        }
        
        $search->where('settings_countries_translations.language_id',($request->get('language') ? $request->get('language') : Language::getDefault()->id));
        
        $search->orderBy('settings_countries.priority','ASC');
        $search->orderBy('settings_countries_translations.translation','ASC');
        
        $results = $search->paginate($perPage);
        $results->appends($request->all());
        
        $formFilters = (new Form('GET',route('admin.settings.countries')))
            ->addOption('cols', 3)
            ->addOption('totalFilters', $filters->count())
            ->addElement('text','term',['label' => 'Search'],['placeholder' => 'search term...','value' => $filters->get('term')])
            ->addElement('select','language',['label' => 'Language','options' => Language::getSelectBox()],['placeholder' => 'filter by language','selected' => ($filters->get('language') ? $filters->get('language') : 1)])
            ->addElement('select','euMember',['label' => 'EU Member','options' => [
                ['value' => 'yes', 'label' => 'Yes'],
                ['value' => 'no', 'label' => 'No'],
            ]],['placeholder' => 'choose','selected' => $filters->get('euMember')]);
        
        $table = (new Table())
            ->addOption('pageTitle', 'Countries')
            ->addOption('topmenu',$this->topmenu)
            ->addOption('totalResults',$results->total())
            ->addOption('filters', $formFilters->make('filters'))
            ->addResults($results);
        
        $table->addColumns([
            'priority' => ['name' => 'Priority'],
            'translation' => ['name' => 'Name','route' => 'route_edit'],
            'country_code' => ['name' => 'Code', 'callback' => function($item){
                return $item->country_code;
            }],
            'phone_code' => ['name' => 'Phone code', 'callback' => function($item){
                return '+'.$item->phone_code;
            }],
            'eu_member' => ['name' => 'EU Member', 'callback' => function($item){
                return ($item->eu_member ? '<span class="text-success"><i class="fa fa-check"></i> Yes</span>' : '<span class="text-muted"><i class="fa fa-ban"></i> No</span>');
            }],
        ]);
        
        $table->setCallback('route_edit',function($item){
            return route('admin.settings.countries.edit',['country' => $item->id]);
        });
        
        $table->addRoute('destroy',[
            'message' => function($item){
                return 'Are you sure you want to delete '.$item->name.'?';
            },
            'url' => function($item){
                return route('admin.settings.countries.destroy',['country' => $item->id]);
            },
            'returnTo' => function(){
                return route('admin.settings.countries');
            }
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'settings',
            'activeSubMenu' => 'countries',
            'content' => $table
        ]);
    }
    
    public function create(){
        $form = (new Form('POST',route('admin.settings.countries.store'),'horizontal'))
            ->addElement('text','country_code',['label' => 'Country Code (ISO2)','required' => true],['placeholder' => 'enter country code'])
            ->addElement('text','phone_code',['label' => 'Phone Code'],['placeholder' => 'enter phone code'])
            ->addElement('text','priority',['label' => 'Priority'],['placeholder' => 'enter priority'])
            ->addElement('checkbox','eu_member',['title' => 'EU member'],['value' => 'yes'])
            ->addElement('break','break1');
        
        foreach(Language::all() as $row){
            $form->addElement('text','translations['.$row->iso2.']',['label' => $row->name,'required' => ($row->is_default ? true : false)],['placeholder' => 'enter translation']);
        }
        
        $form->addOption('pageTitle','New Country');
        $form->addOption('topmenu',$this->topmenu);
        
        return view('components.templates.page',[
            'activeMenu' => 'settings',
            'activeSubMenu' => 'countries',
            'content' => $form
        ]);
    }
    
    public function store(Request $request){
        $validator = \Validator::make($request->all(),[
            'country_code' => 'required|size:2',
        ]);
        
        $errors = $validator->messages();
        
        if(!$errors->has('country_code')){
            if(Country::where('country_code',$request->get('country_code'))->first()){
                $errors->add('country_code', 'This country code already exists');
            }
        }
        
        if(count($errors->all()) === 0){
            $defaultLanguage = Language::getDefault();
            foreach($request->get('translations') as $code => $translation){
                if($code === $defaultLanguage->iso2 && strlen($translation) === 0){
                    $errors->add('translations['.$code.']',$defaultLanguage->name.' is required because it\'s default');
                }
            }
        }
        
        
        if(count($errors->all()) === 0){
            
            print_r($request->all());
            
            exit;
            $language = new Language();
            $language->name = $request->get('name');
            $language->iso2 = $request->get('iso2');
            $language->save();
            
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
}