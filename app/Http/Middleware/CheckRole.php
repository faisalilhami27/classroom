<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
  /**
   * Handle an incoming request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Closure $next
   * @param $roles
   * @return mixed
   */
  public function handle($request, Closure $next, $roles = null)
  {
    if (is_null($request->user())) {
      return redirect()->route('forbidden.index');
    }

    if ($request->user()->hasAnyRole($roles)) {
      return $next($request);
    }

    return redirect()->route('forbidden.index');
  }
}
