<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserControllers extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['success' => true, 'data' => $users ], 200);
    }
    public function register(Request $request)
    {
        // Validasi input
        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:16|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'no_telepon' => 'required|string|regex:/^\+?[0-9]{8,15}$/',
            'role' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);
    
        // Jika validasi gagal, kembalikan pesan error
        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors()], 400);
        }
    
        // Jika role tidak ada, set default role
        $role = $request->role ?? 'masyarakat';
    
        try {
            // Proses upload gambar jika ada
            $profile_picture_url = null;  // Inisialisasi variabel URL gambar
    
            if ($request->hasFile('profile_picture')) {
                // Upload gambar ke Cloudinary
                $uploadedFile = Cloudinary::upload($request->file('profile_picture')->getRealPath());
    
                // Ambil URL gambar yang diupload
                $profile_picture_url = $uploadedFile->getSecurePath();
            }
    
            // Simpan data pengguna ke database
            $user = User::create([
                'nama' => $request->nama,
                'nik' => $request->nik,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'no_telepon' => $request->no_telepon,
                'role' => $role,
                'profile_picture' => $profile_picture_url
            ]);
    
            return response()->json([
                'message' => 'User berhasil ditambahkan.',
                'user' => $user,
                'success' => true
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat menyimpan data. ' . $e->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        // Validasi input
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
    
        // Jika validasi gagal
        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors()], 400);
        }
    
        try {
            // Cek apakah email ada di database
            $user = User::where('email', $request->email)->first();
    
            if (!$user) {
                return response()->json(['message' => 'Email tidak ditemukan'], 404);
            }
    
            // Cek apakah password cocok
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Password salah'], 403);
            }
    
            // Buat token jika autentikasi berhasil
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'token' => $token,
                'user' => $user,
                'success' => true,
                'message' => 'Selamat, Anda berhasil login!',
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function updateUserById(Request $request, $id)
    {
        // Validasi input
        $validated = Validator::make($request->all(), [
            'nama' => 'nullable|string|max:255',
            'email' => "nullable|string|email|max:255|unique:users,email,{$id}",
            'current_password' => 'required_with:password|string|min:8', // Password lama wajib jika ingin mengganti password
            'password' => 'nullable|string|min:8|confirmed', // Konfirmasi password baru
            'no_telepon' => 'nullable|string|max:15|regex:/^\\+?[0-9]{8,15}$/',
            'role' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpg,png,jpg|max:2048'
        ]);
    
        // Jika validasi gagal, kembalikan pesan error
        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors()], 400);
        }
    
        try {
            // Cari user berdasarkan ID
            $user = User::find($id);
    
            // Jika user tidak ditemukan
            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan', 'success' => false], 404);
            }
    
            // Validasi password lama sebelum mengganti password baru
            if ($request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json(['error' => 'Password lama tidak cocok'], 400);
                }
                $user->password = Hash::make($request->password);
            }
    
            // Update data user jika ada perubahan
            $user->nama = $request->nama ?? $user->nama;
            $user->email = $request->email ?? $user->email;
            $user->no_telepon = $request->no_telepon ?? $user->no_telepon;
            $user->role = $request->role ?? $user->role;
    
            // Proses upload gambar jika ada
            if ($request->hasFile('profile_picture')) {
                // Upload gambar baru ke Cloudinary
                $uploadedFile = Cloudinary::upload($request->file('profile_picture')->getRealPath());
                $user->profile_picture = $uploadedFile->getSecurePath();
            }
    
            // Simpan perubahan
            $user->save();
    
            return response()->json([
                'message' => 'User berhasil diperbarui.',
                'user' => $user,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memperbarui data. ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getUserById($id)
    {
    try {
        // Mencari user berdasarkan ID
        $user = User::get()->find($id);

        // Jika user tidak ditemukan
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan', 'success' => false], 404);
        }

        return response()->json(['data' => $user, 'success' => true], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
    }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Berhasil keluar', 'success' => true], 200);
    }
    public function deleteUserById($id)
    {
        try {
            // Cari user berdasarkan ID
            $user = User::find($id);

            // Jika user tidak ditemukan
            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan', 'success' => false], 404);
            }

            // Hapus user
            $user->delete();

            return response()->json([
                'message' => 'User berhasil dihapus.',
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat menghapus data. ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

}
