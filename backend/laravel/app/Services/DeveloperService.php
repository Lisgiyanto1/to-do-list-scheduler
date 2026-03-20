<?php

namespace App\Services;

use App\Models\Developer;
use Illuminate\Support\Facades\Cache;

class DeveloperService
{
    public function getByUserId(string $userId)
    {
        return Cache::remember("developer:user:$userId", 60, function () use ($userId) {
            return Developer::where('user_id', $userId)
                ->select(['id', 'user_id', 'profile_picture', 'status_akun'])
                ->first();
        });
    }

    public function updateProfile(string $userId, array $data)
    {
        $developer = Developer::where('user_id', $userId)->firstOrFail();
        $developer->update($data);

        Cache::forget("developer:user:$userId");

        return $developer;
    }
}