<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DeveloperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DeveloperController extends Controller
{
    public function __construct(private DeveloperService $developerService)
    {
    }

    /**
     * Get developer profile by authenticated user
     */
    public function profile(Request $request)
    {
        $developer = $this->developerService->getByUserId($request->user()->id);

        if (!$developer) {
            return response()->json([
                'success' => false,
                'message' => 'Developer profile not found'
            ], 404);
        }

        // Tambahkan URL publik foto
        $developer->profile_picture_url = $developer->profile_picture
            ? asset('storage/' . $developer->profile_picture)
            : null;

        return response()->json([
            'success' => true,
            'data' => $developer
        ]);
    }

    /**
     * Upload or update profile picture
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $userId = $request->user()->id;
        $developer = $this->developerService->getByUserId($userId);

        if (!$developer) {
            return response()->json([
                'success' => false,
                'message' => 'Developer profile not found'
            ], 404);
        }

        // Hapus file lama jika ada
        if ($developer->profile_picture && Storage::disk('public')->exists($developer->profile_picture)) {
            Storage::disk('public')->delete($developer->profile_picture);
        }

        // Simpan file baru
        $file = $request->file('profile_picture');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile_pictures', $filename, 'public');

        // Update database
        $developer = $this->developerService->updateProfile($userId, [
            'profile_picture' => $path,
        ]);

        // Tambahkan URL publik
        $developer->profile_picture_url = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'message' => 'Profile picture updated successfully',
            'data' => $developer
        ]);
    }

    /**
     * Update developer status akun
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status_akun' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $userId = $request->user()->id;
        $developer = $this->developerService->updateProfile($userId, [
            'status_akun' => $request->status_akun,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status akun updated successfully',
            'data' => $developer
        ]);
    }
}

