<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Hall;

class HallMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $availableHalls = Hall::all();
        if ($availableHalls->isEmpty()) {
            $availableHalls = collect([Hall::firstOrCreate(
                ['id' => 1],
                ['name' => 'মমতা কমিউনিটি সেন্টার', 'capacity' => 500, 'price_per_slot' => 30000, 'is_active' => true]
            )]);
        }

        if ($request->hasSession()) {
            $defaultHall = $availableHalls->first();
            session()->put('active_hall_id', $defaultHall->id);
            view()->share('availableHalls', $availableHalls);
            view()->share('activeHall', $defaultHall);
        }

        return $next($request);
    }
}
