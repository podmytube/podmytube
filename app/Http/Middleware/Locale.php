<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Config;
use Session;
use Illuminate\Support\Facades\Auth;

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
			if (isset(Auth::user()->language) && in_array(Auth::user()->language, Config::get('app.locales'))) {
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
