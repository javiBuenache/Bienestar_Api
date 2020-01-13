<?php

namespace App\Http\Middleware;
use App\Helpers\Token;
use App\User;
use Closure;

class CheckAuth
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
        $user = new User();
        if ($user->is_authorized($request)) 
        {
            $header = $request->header("Authorization");    
            $token = new Token();
            $decodedToken = $token->decode($header);
            $request->request->add(['data' => $decodedToken]);
            return $next($request);
        }
        return response()->json(['message' => 'Error, no tiene los permisos'], 401);
    }
}

