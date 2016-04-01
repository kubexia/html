<?php namespace App\Http\Controllers\Admin\Demo;

use App\Http\Base\Admin as BaseController;

use Illuminate\Http\Request;

use App\Libraries\HTML\Form;

class Forms extends BaseController {
    
    public function __construct(){
        parent::__construct();
        
        $this->topMenu = [
            [
                'title' => 'CRUD Forms', 'url' => '#', 'icon' => 'fa-edit',
                'submenu' => [
                    ['title' => 'Form horizontal 2:10', 'url' => route('admin.demo.forms.single',['type' => 'horizontal','grid' => '2:10'])],
                    ['title' => 'Form horizontal 4:8', 'url' => route('admin.demo.forms.single',['type' => 'horizontal','grid' => '4:8'])],
                    ['title' => 'Form horizontal 6:6', 'url' => route('admin.demo.forms.single',['type' => 'horizontal','grid' => '6:6'])],
                    
                    ['divider' => 'before','title' => 'Form basic', 'url' => route('admin.demo.forms.single',['type' => 'basic'])],
                    ['title' => 'Form basic, 3 cols', 'url' => route('admin.demo.forms.single',['type' => 'basic','cols' => 3])],
                    
                    ['divider' => 'before','title' => 'Form inline', 'url' => route('admin.demo.forms.single',['type' => 'inline'])],
                ]
            ],
            [
                'title' => 'Filtering Form', 'url' => route('admin.demo.forms.filters'), 'icon' => 'fa-filter',
            ],
        ];
    }

    public function index(){
        return view('contents.admin.demo.forms.forms');
    }
    
    public function single(Request $request){
        $type = ($request->has('type') ? $request->get('type') : 'horizontal');
        $grid = ($request->get('grid') ? explode(':',$request->get('grid')) : [2,10]);
        $cols = ($request->has('cols') ? $request->get('cols') : NULL);
        
        $form = (new Form('POST','#',$type, $grid));
        
        switch($type){
            case "horizontal":
                $form->addOption('pageTitle','CRUD: Form horizontal');
                break;
            
            case "basic":
                $form->addOption('pageTitle','CRUD: Form basic');
                if(!is_null($cols)){
                    $form->addOption('cols', $cols);
                }
                break;
                
            case "inline":
                $form->addOption('pageTitle','CRUD: Form inline');
                break;
        }
        
        $form
            ->addOption('id', 'formWithId')
            //select boxes
            ->addElement('select','selectbox',['label' => 'Select box', 'required' => true,'options' => [
                ['value' => 1, 'label' => 'Value 1'],
                ['value' => 2, 'label' => 'Value 2'],
                ['value' => 3, 'label' => 'Another value'],
            ]],['placeholder' => 'choose'])
            ->addElement('select','selectbox_selected',['label' => 'Select box selected','options' => [
                ['value' => 1, 'label' => 'Value 1'],
                ['value' => 2, 'label' => 'Value 2'],
                ['value' => 3, 'label' => 'Another value'],
            ]],['placeholder' => 'choose','selected' => 2])
            
            //select boxes multiple
            ->addElement('select','selectboxmultiple',['label' => 'Select box (multiple)', 'required' => true,'options' => [
                ['value' => 1, 'label' => 'Value 1'],
                ['value' => 2, 'label' => 'Value 2'],
                ['value' => 3, 'label' => 'Another value'],
            ]],['placeholder' => 'choose','multiple' => true])
            ->addElement('select','selectboxmultiple_selected',['label' => 'Select box (multiple) selected','options' => [
                ['value' => 1, 'label' => 'Value 1'],
                ['value' => 2, 'label' => 'Value 2'],
                ['value' => 3, 'label' => 'Another value'],
            ]],['placeholder' => 'choose','multiple' => true,'selected' => 1])
            
            //text inputs
            ->addElement('text','text_input',['label' => 'Text input','required' => true],['placeholder' => 'enter value'])
            ->addElement('text','text_input_value',['label' => 'Text input with value','required' => true],['placeholder' => 'enter value','value' => 'value here'])
            ->addElement('password','password_input',['label' => 'Password input'],['placeholder' => 'enter value'])
            ->addElement('password','password_input2',['label' => 'Password input2','generator' => true],['placeholder' => 'enter value','id' => 'passwordId'])
            
                
            //textareas
            ->addElement('textarea','textarea',['label' => 'Textarea'],['placeholder' => 'enter value','rows' => 2])
            ->addElement('textarea','textarea_fullwidth',['label' => 'Textarea full width','fullWidth' => true],['placeholder' => 'enter value','rows' => 2])
            
                
            //file uploads
            ->addElement('imageUpload','image_upload',['label' => 'Image upload'],['placeholder' => 'Choose image...'])
            ->addElement('fileUpload','file_upload',['label' => 'File upload'],['placeholder' => 'Choose file...'])
                
            //checkboxes
            ->addElement('checkbox','checkbox1',['label' => 'Checkbox','title' => 'Check me out with label'])
            ->addElement('checkbox','checkbox2',['title' => 'Check me out without label'])
            ->addElement('checkbox','checkbox3',['title' => 'Checkbox checked'],['checked' => true])
            ->addElement('checkboxes','checkboxes',['label' => 'Checkboxes', 'options' => [
                ['value' => 1, 'title' => 'Checkbox 1','readonly' => true],
                ['value' => 2, 'title' => 'Checkbox 2'],
                ['value' => 3, 'title' => 'Another checkbox'],
            ]],['selected' => [1,2]])
            ->addElement('checkboxes','checkboxes_inline',['label' => 'Checkboxes inline', 'options' => [
                ['value' => 1, 'title' => 'Checkbox 1','readonly' => true],
                ['value' => 2, 'title' => 'Checkbox 2'],
                ['value' => 3, 'title' => 'Another checkbox'],
            ],'inline' => true],['selected' => [1,2]])
                
            //radios
            ->addElement('radio','radio1',['label' => 'Radio','title' => 'Check me out with label'])
            ->addElement('radio','radio2',['title' => 'Check me out without label'])
            ->addElement('radio','radio3',['title' => 'Radio checked'],['checked' => true])
            ->addElement('radios','radios',['label' => 'Radios', 'options' => [
                ['value' => 1, 'title' => 'Radio 1','readonly' => true],
                ['value' => 2, 'title' => 'Radio 2'],
                ['value' => 3, 'title' => 'Another Radio'],
            ]],['selected' => [1,2]])
            ->addElement('radios','radios_inline',['label' => 'Radios inline', 'options' => [
                ['value' => 1, 'title' => 'Radio 1','readonly' => true],
                ['value' => 2, 'title' => 'Radio 2'],
                ['value' => 3, 'title' => 'Another Radio'],
            ],'inline' => true],['selected' => [1,2]])
            ;
        
        $form->addOption('topmenu',$this->topMenu);
        
        $form->addRoute('destroy',[
            'item' => NULL,
            'message' => function($item){
                //return 'Are you sure you want to delete '.$item->name.'?';
                return 'Are you sure you want to delete '.(isset($item->name) ? $item->name : 'this item').'?';
            },
            'url' => function($item){
                return '#'; //route to destroy
            },
            'returnTo' => function(){
                return '#'; //route to return
            },
        ]);
        
        return view('components.templates.page',[
            'activeMenu' => 'demo',
            'activeSubMenu' => 'forms',
            'content' => $form
        ]);
    }
    
    public function filters(Request $request){
        $type = 'filters';
        $cols = ($request->has('cols') ? $request->get('cols') : 3);
        
        $form = (new Form('POST','#',$type));
        $form->addOption('cols', $cols);
        
        $form->addOption('pageTitle','Filter Form');
        
        $form
            //select boxes
            ->addElement('select','selectbox',['label' => 'Select box', 'required' => true,'options' => [
                ['value' => 1, 'label' => 'Value 1'],
                ['value' => 2, 'label' => 'Value 2'],
                ['value' => 3, 'label' => 'Another value'],
            ]],['placeholder' => 'choose'])
            
            //select boxes multiple
            ->addElement('select','selectboxmultiple',['label' => 'Select box (multiple)', 'required' => true,'options' => [
                ['value' => 1, 'label' => 'Value 1'],
                ['value' => 2, 'label' => 'Value 2'],
                ['value' => 3, 'label' => 'Another value'],
            ]],['placeholder' => 'choose','multiple' => true])
            
            //text inputs
            ->addElement('text','text_input',['label' => 'Text input','required' => true],['placeholder' => 'enter value'])
                
            //text between
            ->addElement('textBetween','value_between',['label' => 'Value between'],['placeholder_from' => 'from','placeholder_to' => 'to','value_from' => '','value_to' => ''])
            
            //calendar
            ->addElement('datepicker','datepicker',['label' => 'Datepicker'],['placeholder' => 'choose date'])
            ->addElement('datepickerBetween','datepicker_between',['label' => 'Datepicker between'],['placeholder_from' => 'from date','placeholder_to' => 'to date'])
                
            //sortby
            ->addElement('selectSort','sortby',['label' => 'Order By','options' => [
                ['value' => 'priority', 'label' => 'Priority'],
                ['value' => 'amount', 'label' => 'Amount'],
                ['value' => 'period', 'label' => 'Period'],
                ['value' => 'created', 'label' => 'Date Created'],
            ]],['placeholder_field' => 'choose sort field', 'placeholder_order' => 'order','selected_field' => '','selected_order' => ''])
            ;
        
        $form->addOption('topmenu',$this->topMenu);
        
        return view('components.templates.page',[
            'activeMenu' => 'demo',
            'activeSubMenu' => 'forms',
            'content' => $form
        ]);
    }
    
    public function multiple(){
        return view('components.templates.page',[
            'activeMenu' => 'demo',
            'activeSubMenu' => 'forms',
            'content' => ''
        ]);
    }
    
}
