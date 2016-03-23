<?php

namespace App\Http\Middleware;

use Closure;

class EnableQueryLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(starts_with($request->root(),'http://localhost:8000')){
            \DB::enableQueryLog();
        }
        return $next($request);
    }
    
}
