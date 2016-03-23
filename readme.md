1. composer require kubexia/kubexia
1.1 nano .env -> enter settings

2. Twig Installation:
    2.1. configs/app.php  -> providers and aliases: TwigBridge\ServiceProvider::class
    2.2. php artisan vendor:publish --provider="TwigBridge\ServiceProvider"
    2.3. go to configs/twigbridge.php , find 'functions' then add:
    
    <code>
    'theme' => function($string='',$parameters = [], $secure = null){
        return url('themes/'.config('section').'/'.config('theme').'/'.$string,$parameters, $secure);
    }
    </code>

3. Kubexia Installation
    2.1. configs/app.php  -> providers: Kubexia\ServiceProvider::class
    2.2. php artisan vendor:publish --provider="Kubexia\ServiceProvider"
    2.3. go to /public and run "bower install"