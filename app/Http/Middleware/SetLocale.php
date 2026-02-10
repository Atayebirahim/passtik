<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    protected $rtlLocales = ['ar', 'he', 'fa', 'ur'];

    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale', config('app.locale'));
        
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
        }
        
        App::setLocale($locale);
        
        $isRtl = in_array($locale, $this->rtlLocales);
        view()->share('isRtl', $isRtl);
        view()->share('currentLocale', $locale);
        
        return $next($request);
    }
}
