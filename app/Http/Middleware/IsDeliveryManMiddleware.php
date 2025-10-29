<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class IsDeliveryManMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Allowed user types
        $allowedTypes = [User::TYPE_DELIVERY_MAN];

        // Check if the user's type is in the allowed list
        if (!in_array(Auth::user()->type, $allowedTypes)) {
            // If using Passport, revoke token
            $this->tokenRevoke();
            return response()->json(['error' => 'Access denied. You do not have the required user type.'], 403);
        }
        // Proceed with the request
        return $next($request);
    }

    public function tokenRevoke(){
        if (method_exists(Auth::user(), 'token')) {
            Auth::user()->token()->revoke();
        }
    }
}
