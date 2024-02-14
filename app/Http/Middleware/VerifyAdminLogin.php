<?php

namespace App\Http\Middleware;

use Closure;

class VerifyAdminLogin
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
        if(session('admin') == null)
        {
            return redirect(route('adminLogin'));
        }
        return $next($request);
    }
}
