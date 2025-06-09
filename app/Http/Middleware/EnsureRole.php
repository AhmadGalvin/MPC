<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;
use Illuminate\Support\Str;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return $this->handleUnauthorized('Unauthenticated.');
        }

        // Convert comma-separated roles to array
        $allowedRoles = collect($roles)
            ->flatMap(fn($role) => Str::contains($role, ',') ? explode(',', $role) : [$role])
            ->map(fn($role) => trim($role))
            ->filter()
            ->values()
            ->toArray();

        $userRole = $request->user()->role->value;
        
        if (!in_array($userRole, $allowedRoles)) {
            return $this->handleUnauthorized(
                'Unauthorized. Required roles: ' . implode(', ', $allowedRoles),
                $request->expectsJson()
            );
        }

        return $next($request);
    }

    /**
     * Handle unauthorized access based on request type
     */
    private function handleUnauthorized(string $message, bool $isJson = false): Response
    {
        if ($isJson) {
            return response()->json([
                'status' => 'error',
                'message' => $message
            ], 403);
        }

        abort(403, $message);
    }
} 