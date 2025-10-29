<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Passport\Token;
use Illuminate\Auth\AuthenticationException;

class CheckPassportToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

            // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated. Please log in again.',
                'status' => 401
            ], 401);
        }

        $user = auth()->user();

        // Fetch the user's latest access token
        $oauthAccessToken = DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->where('revoked', false)
            ->latest('created_at')
            ->first();

        // Check if token exists and if it's expired
        if (!$oauthAccessToken || now()->greaterThan($oauthAccessToken->expires_at)) {
            return response()->json([
                'message' => 'Token expired. Please refresh your token.',
                'status' => 401
            ], 401);
        }


        return $next($request);
    }
}
