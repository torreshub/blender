<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (!current_user()) {
            return $this->handleUnauthorizedRequest($request);
        }

        current_user()->registerLastActivity()->save();

        return $next($request);
    }

    protected function handleUnauthorizedRequest($request)
    {
        if ($request->ajax()) {
            return response('Unauthorized.', 401);
        }

        if ($request->isFront()) {
            return redirect()->guest(action('Front\AuthController@getLogin'));
        }

        if ($request->isBack()) {
            return redirect()->guest(action('Back\AuthController@getLogin'));
        }

        throw new Exception('Invalid auth guard specified');
    }
}
