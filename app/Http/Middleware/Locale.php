<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Config;
use Illuminate\Support\Facades\Auth;
use Session;

class Locale
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
        /** if user authenticated and had a language set, we use it */
        if (Auth::check()) {
            if (
                isset(Auth::user()->language) &&
                in_array(Auth::user()->language, Config::get('app.locales'))
            ) {
                $locale = Auth::user()->language;
                App::setLocale($locale);
                return $next($request);
            }
        }

        $raw_locale = Session::get('locale');
        if (in_array($raw_locale, Config::get('app.locales'))) {
            $locale = $raw_locale;
        } else {
            $locale = Config::get('app.locale');
        }

        App::setLocale($locale);
        return $next($request);
    }
}
