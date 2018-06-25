<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Http\Response;

class ApiAuth
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
        ['id' => $id, 'api_token' => $api_token] = $request->only('api_token', 'id');
        if (empty($api_token) || empty($id)) {
            return response([
                'status' => Response::HTTP_BAD_REQUEST,
                'error' => 'api_token or id is empty',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = \App\User::find($id);

        if ($user === null) {
            return response([
                'status' => Response::HTTP_BAD_REQUEST,
                'error' => 'No user for this ID',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($user->api_token !== $api_token) {
            return response([
                'error' => 'Wrong API_TOKEN',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
