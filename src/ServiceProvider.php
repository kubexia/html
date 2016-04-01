<?php
namespace Kubexia;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    
    protected $packageName = 'kubexia';


    public function boot() {
        $this->handleConfigs();
        
        //$this->handleMigrations();
        
        $this->handleViews();
        
        $this->handleTranslations();
        
        //$this->handleRoutes();
        
        $this->handleThemes();
        
        $this->handleComponents();
        
        $this->registerTwigFunctions();
        
        $this->removeFiles();
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // Bind any implementations.
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }
    
    private function handleConfigs() {
        $configPath = __DIR__ . '/../config/'.$this->packageName.'.php';
        
        $this->publishes([
            $configPath => config_path($this->packageName.'.php')
        ],'configs');
        
        $this->mergeConfigFrom($configPath, $this->packageName);
    }
    
    private function handleTranslations() {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', $this->packageName);
        
        $this->publishes([
            __DIR__.'/../resources/lang' => base_path('resources/lang/vendor/'.$this->packageName)
        ],'translations');
    }
    
    private function handleViews() {
        $this->loadViewsFrom(__DIR__.'/../resources/views', $this->packageName);
        
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/'.$this->packageName)
        ],'views');
        
    }
    
    private function handleMigrations() {
        $this->publishes([
            __DIR__ . '/../migrations' => base_path('database/migrations')
        ],'migrations');
    }
    
    private function handleRoutes() {
        $this->publishes([
            __DIR__ . '/../src/Http/routes.php' => app_path('Http/Routes')
        ],'routes');
    }
    
    private function handleThemes(){
        $this->publishes([
            __DIR__.'/../public' => public_path(),
        ], 'public');
    }
    
    private function handleComponents(){
        //Libraries
        $this->publishes([
            __DIR__.'/../src/Libraries' => app_path('Libraries'),
        ],'libraries');
        
        //Models
        $this->publishes([
            __DIR__.'/../src/Models' => app_path('Models'),
        ],'models');
        
        
        //HTTP Components
        $http = [
            'controllers' => 'Controllers',
            'baseControllers' => 'Base',
            'middleware' => 'Middleware',
            'routesComponents' => 'Routes',
            'routes' => 'routes.php',
        ];
        
        foreach($http as $name => $path){
            $this->publishes([
                __DIR__.'/../src/Http/'.$path => app_path('Http/'.$path),
            ],$name);
        }
    }
    
    private function removeFiles(){
        if(!file_exists(config_path($this->packageName.'.php'))){
            $files = [
                app_path('User.php'),
                app_path('Http/Controllers/Auth'),
            ];
            
            foreach(scandir(base_path('database/migrations')) as $item){
                if(preg_match('#.php#',$item)){
                    $files[] = base_path('database/migrations').'/'.$item;
                }
            }
            
            foreach($files as $filename){
                if(is_dir($filename)){
                    rmdir($filename);
                }
                else{
                    if(file_exists($filename)){
                        unlink($filename);
                    }
                }
            }
        }
    }
    
    private function registerTwigFunctions(){
        $twig = app('twig');
        
        $twig->addFunction(new \Twig_SimpleFunction('theme', function($string='',$parameters = [], $secure = null){
            return url('themes/'.config('section').'/'.config('theme').'/'.$string,$parameters, $secure);
        }));
    }
}
