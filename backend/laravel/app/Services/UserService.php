<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getById(string $id)
    {
        return Cache::remember("user:$id", 60, function () use ($id) {
            return User::with('developer:id,user_id,profile_picture,status_akun')
                ->select(['id', 'name', 'email'])
                ->findOrFail($id);
        });
    }

    public function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->developer()->create([
            'status_akun' => 'active',
            'profile_picture' => null,
        ]);

        return $user->load('developer:id,user_id,profile_picture,status_akun');
    }

    public function update(string $id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);

        Cache::forget("user:$id");

        return $user->load('developer:id,user_id,profile_picture,status_akun');
    }

    public function delete(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        Cache::forget("user:$id");

        return true;
    }


    // Tambahkan method ini di dalam class UserService
    public function getCurrentDeveloper(string $id)
    {
        // Menggunakan getById yang sudah Anda buat karena sudah mem-filter password 
        // melalui select(['id', 'name', 'email']) dan menyertakan relasi developer.
        return $this->getById($id);
    }
}