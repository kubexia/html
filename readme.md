#Laravel Components

1. composer require kubexia/kubexia 1
1. nano .env -> enter settings 1.1

2. Twig Installation: 2
1. configs/app.php  -> providers and aliases: TwigBridge\ServiceProvider::class 2.1
2. php artisan vendor:publish --provider="TwigBridge\ServiceProvider" 2.2
    
3. Kubexia Installation 3
1. configs/app.php  -> providers: Kubexia\ServiceProvider::class 3.1
2. php artisan vendor:publish --provider="Kubexia\ServiceProvider" 3.2
3. go to /public and run "bower install" 3.3