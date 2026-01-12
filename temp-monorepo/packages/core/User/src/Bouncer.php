<?php

namespace Webkul\User;

class Bouncer
{
    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public function hasPermission($permission)
    {
        $user = auth()->guard('admin')->user();
        
        if (!$user) {
            return false;
        }

        // Check if user has a role and permission_type is 'all'
        if ($user->role && $user->role->permission_type === 'all') {
            return true;
        }

        // If no role or permission_type is not 'all', check specific permission
        return $user->role 
            && method_exists($user, 'hasPermission') 
            && $user->hasPermission($permission);
    }

    /**
     * Checks if user allowed or not for certain action
     *
     * @param  string  $permission
     * @return void
     */
    public static function allow($permission)
    {
        if (
            ! auth()->guard('admin')->check()
            || ! auth()->guard('admin')->user()->hasPermission($permission)
        ) {
            abort(401, 'This action is unauthorized');
        }
    }
}
