<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
    

            if ($role == "ADMIN") {
                return $next($request);
            }
            if ($role == "TREASURER") {
                return redirect()->route("dashboard");  
            }
            if ($role == "REPRESENTATIVE") {
                return redirect()->route("repdashboard");  
            }
            if ($role == "STUDENT") {
                return redirect()->route("StudentDashboard");  
            }
    
    
    
        }
    
        return redirect()->route("login");  
    }
}
