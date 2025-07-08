<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TreasurerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('role')) {
            $role = session('role');
    

            if ($role == "TREASURER") {
                return $next($request);
            }
            if ($role == "STUDENT") {
                return redirect()->route("StudentDashboard");  
            }
          
            if ($role == "ADMIN") {
                return redirect()->route("repdashboard");  
            }
    
            if ($role == "REPRESENTATIVE") {
                return redirect()->route("repdashboard");  
            }
    
    
        }
    
        return redirect()->route("login");  
    }
}
