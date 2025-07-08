<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
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
    

            if ($role == "STUDENT") {
                return $next($request);
            }
            if ($role == "ADMIN") {
                return redirect()->route("AdminDashboard");  
            }
          
            if ($role == "REPRESENTATIVE") {
                return redirect()->route("repdashboard");  
            }
            if ($role == "TREASURER") {
                return redirect()->route("dashboard");  
            }
    
    
    
        }
    
        return redirect()->route("login");  
    }

}