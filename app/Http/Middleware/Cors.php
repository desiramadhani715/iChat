<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
        ->header('Access-Control-Allow-Origin', 'http://127.0.0.1:8000, http://localhost:8080, https://booking.makutapro.id, https://api-booking.makutapro.id')
        ->header('Access-Control-Allow-Credentials', true)
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT,DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'X-Requested-With,Content-Type,Authorization');
    }
}
