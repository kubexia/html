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
        
        // $this->handleMigrations();
        
        $this->handleViews();
        
        $this->handleTranslations();
        
        // $this->handleRoutes();
        
        $this->handleAssets();
        
        $this->handleComponents();
        
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
        
        $this->publishes([$configPath => config_path($this->packageName.'.php')]);
        
        $this->mergeConfigFrom($configPath, $this->packageName);
    }
    
    private function handleTranslations() {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', $this->packageName);
        
        $this->publishes([__DIR__.'/../resources/lang' => base_path('resources/lang/vendor/'.$this->packageName)]);
    }
    
    private function handleViews() {
        $this->loadViewsFrom(__DIR__.'/../resources/views', $this->packageName);
        
        $this->publishes([__DIR__.'/../resources/views' => base_path('resources/views/vendor/'.$this->packageName)]);
        
    }
    
    private function handleMigrations() {
        $this->publishes([__DIR__ . '/../migrations' => base_path('database/migrations')]);
    }
    
    private function handleRoutes() {
        //include __DIR__.'/../src/Http/routes.php';
    }
    
    private function handleAssets(){
        $this->publishes([
            __DIR__.'/../public' => public_path(),
        ], 'kubexia');
    }
    
    private function handleComponents(){
        $this->publishes([
            __DIR__.'/../src/Libraries' => app_path('Libraries'),
        ]);
        
        $this->publishes([
            __DIR__.'/../src/Models' => app_path('Models'),
        ]);
        
        $this->publishes([
            __DIR__.'/../src/Http' => app_path('Http'),
        ]);
    }
    
    private function removeItems(){
        if(!file_exists(config_path($this->packageName.'.php'))){
            $files = [
                app_path('User.php'),
                app_path('Http/Controllers/Auth/AuthController.php'),
                app_path('Http/Controllers/Auth/PasswordController.php'),
                app_path('Http/Controllers/Controller.php'),
            ];
            
            foreach(scandir(base_path('database/migrations')) as $item){
                if(preg_match('#.php#',$item)){
                    $files[] = base_path('database/migrations').'/'.$item;
                }
            }
            
            foreach($files as $filename){
                if(file_exists($filename)){
                    unlink($filename);
                }
            }
        }
    }
}
