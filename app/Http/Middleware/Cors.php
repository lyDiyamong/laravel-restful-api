<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors  
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        $allowedOrigins = [
            'http://localhost:5173',
            'http://yourdomain.com', 
            // Add other origins as needed
        ];
        
        $origin = $request->header('Origin');
        
        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }
        
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Mong-Application');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        if ($request->method() === 'OPTIONS') {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $origin ?? '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Mong-Application')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}