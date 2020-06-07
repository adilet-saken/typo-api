<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class Authenticate
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
        $token = $request->header('X-Token');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.'
            ], 401);
        }

        $user = User::firstWhere('token', $token);

        if (!$user) {
            $user = new User;
            $user->token = $token;
            $user->save();
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
