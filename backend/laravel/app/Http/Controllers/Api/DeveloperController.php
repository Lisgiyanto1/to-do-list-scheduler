<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
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


    public function update(Request $request)
    {
        try {
            // Ambil ID user terlebih dahulu untuk menghindari 'incomplete object' pada $request->user()
            $userId = $request->user()->id;

            // Ambil ulang user dari database agar mendapatkan instance yang segar (Fresh Instance)
            $user = User::findOrFail($userId);

            // Validasi
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $userId,
                'status_akun' => 'required|in:active,busy,offline',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            // 1. Update Tabel Users (Nama & Email)
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // 2. Ambil data developer
            $developer = $this->developerService->getByUserId($userId);

            if (!$developer) {
                return response()->json(['success' => false, 'message' => 'Developer profile not found'], 404);
            }

            // 3. Persiapkan data untuk Tabel Developer
            $developerData = [
                'status_akun' => $request->status_akun,
            ];

            // 4. Logika Upload Foto
            if ($request->hasFile('profile_picture')) {
                // Hapus foto lama jika ada di storage
                if ($developer->profile_picture && Storage::disk('public')->exists($developer->profile_picture)) {
                    Storage::disk('public')->delete($developer->profile_picture);
                }

                $file = $request->file('profile_picture');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_pictures', $filename, 'public');

                $developerData['profile_picture'] = $path;
            }

            // 5. Update via Service
            $updated = $this->developerService->updateProfile($userId, $developerData);

            // Sertakan URL gambar di response
            $updated->profile_picture_url = $updated->profile_picture
                ? asset('storage/' . $updated->profile_picture)
                : null;

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'data' => $updated
            ]);

        } catch (\Exception $e) {
            // Log error untuk debug internal
            \Log::error("Update Profile Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

