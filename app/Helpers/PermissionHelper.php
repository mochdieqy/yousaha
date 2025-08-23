<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if the authenticated user has a specific permission
     */
    public static function userCan(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Company owners have all permissions
        if ($user->hasRole('Company Owner')) {
            return true;
        }

        return $user->hasPermissionTo($permission);
    }

    /**
     * Check if the authenticated user has any of the given permissions
     */
    public static function userCanAny(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Company owners have all permissions
        if ($user->hasRole('Company Owner')) {
            return true;
        }

        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if the authenticated user has all of the given permissions
     */
    public static function userCanAll(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Company owners have all permissions
        if ($user->hasRole('Company Owner')) {
            return true;
        }

        return $user->hasAllPermissions($permissions);
    }

    /**
     * Check if the authenticated user has a specific role
     */
    public static function userHasRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole($role);
    }

    /**
     * Check if the authenticated user has any of the given roles
     */
    public static function userHasAnyRole(array $roles): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Company owners have all permissions
        if ($user->hasRole('Company Owner')) {
            return true;
        }

        return $user->hasAnyRole($roles);
    }

    /**
     * Get the authenticated user's roles
     */
    public static function getUserRoles(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Auth::user()->getRoleNames()->toArray();
    }

    /**
     * Get the authenticated user's permissions
     */
    public static function getUserPermissions(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Auth::user()->getAllPermissions()->pluck('name')->toArray();
    }
}
