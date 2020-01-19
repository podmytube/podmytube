<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Gate;

class UserMustOwnChannel
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
        if (($channel = $request->route()->parameter('channel')) && Gate::denies('owns', $channel)) {
            abort(403, "Access denied");
        }
        return $next($request);
    }
}
