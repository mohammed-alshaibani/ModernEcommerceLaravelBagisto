<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Auth\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    public function updateProfile(Model $user, array $data): Model
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user;
    }

    public function completeProfile(Model $user, array $data): Model
    {
        if ($user->profile_completed_at) {
            return $user;
        }

        // Add additional validation or logic here
        $user->update(array_merge($data, ['profile_completed_at' => now()]));
        return $user;
    }
}
