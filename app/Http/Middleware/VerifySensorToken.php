<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySensorToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.sensor.token');
        $provided = (string) ($request->header('X-Sensor-Token') ?? $request->query('sensor_token', ''));

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Invalid sensor token.'], 401);
        }

        return $next($request);
    }
}
