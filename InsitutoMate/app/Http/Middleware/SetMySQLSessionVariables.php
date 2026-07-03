<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetMySQLSessionVariables
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            \Illuminate\Support\Facades\DB::unprepared("SET @usuario_id = " . auth()->id());
        }
        
        $ip = $request->ip() ?? '127.0.0.1';
        \Illuminate\Support\Facades\DB::unprepared("SET @user_ip = '" . $ip . "'");
        
        return $next($request);
    }
}
