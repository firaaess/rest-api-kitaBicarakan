<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // Pastikan Cloudinary terimport

class PengaduanControllers extends Controller
{
    public function index(Request $request)
{
    // Mendapatkan role user yang sedang login
    $userRole = $request->user()->role;

    // Query pengaduan
    $pengaduanQuery = Pengaduan::with(['user', 'lokasi', 'kategory']);

    // Jika user adalah petugas, filter pengaduan yang bukan berstatus 'proses'
    if ($userRole === 'petugas') {
        $pengaduanQuery->where('status', '!=', 'proses');
    }

    // Eksekusi query dan ambil hasil
    $pengaduan = $pengaduanQuery->get();

    return response()->json(['data' => $pengaduan, 'success' => true], 200);
}


    public function addPengaduan(Request $request)
    {
        // Validasi input
        $validated = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'isi_pengaduan' => 'required|string',
            'foto' => 'required|image|mimes:jpg,png,jpg|max:2048',
            'lokasi_id' => 'required|integer',
            'kategori_id' => 'required|integer',
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validated->fails()) {
            return response()->json([$validated->errors()], 403);
        }

        try {
            // Proses upload gambar ke Cloudinary
            $uploadedFile = Cloudinary::upload($request->file('foto')->getRealPath());

            // Mendapatkan URL gambar yang diupload
            $fotoUrl = $uploadedFile->getSecurePath();

            // Simpan data pengaduan ke database
            $pengaduan = Pengaduan::create([
                'user_id' => auth()->user()->id,
                'judul' => $request->judul,
                'isi_pengaduan' => $request->isi_pengaduan,
                'foto' => $fotoUrl,  // Menyimpan URL dari Cloudinary
                'lokasi_id' => $request->lokasi_id,
                'kategori_id' => $request->kategori_id,
                'status' => 'proses'
            ]);

            return response()->json(['message' => 'Pengaduan berhasil ditambahkan', 'success' => true], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data. ' . $e->getMessage()], 500);
        }
    }

    public function getPengaduanByUserId()
    {
        try {
            // Mendapatkan user ID dari token/auth
            $userId = auth()->user()->id;

            // Mengambil data pengaduan berdasarkan user_id
            $pengaduan = Pengaduan::where('user_id', $userId)->get();

            // Jika tidak ada data pengaduan
            if ($pengaduan->isEmpty()) {
                return response()->json(['message' => 'Tidak ada pengaduan yang ditemukan', 'success' => false], 404);
            }

            return response()->json(['data' => $pengaduan, 'success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function getPengaduanById($id)
    {
    try {
        // Mencari pengaduan berdasarkan ID
        $pengaduan = Pengaduan::with(['user','lokasi', 'kategory'])->find($id);

        // Jika pengaduan tidak ditemukan
        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan', 'success' => false], 404);
        }

        return response()->json(['data' => $pengaduan, 'success' => true], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
    }
    }
    public function updateStatusPengaduan(Request $request, $id)
    {
        // Validasi input
        $validated = Validator::make($request->all(), [
            'status' => 'required|string|in:proses,diterima,selesai',
        ]);
    
        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors(), 'success' => false], 422);
        }
    
        try {
            // Cari pengaduan berdasarkan ID
            $pengaduan = Pengaduan::find($id);
    
            if (!$pengaduan) {
                return response()->json(['message' => 'Pengaduan tidak ditemukan', 'success' => false], 404);
            }
    
            // Update status pengaduan
            $pengaduan->status = $request->status;
            $pengaduan->save();
    
            return response()->json(['message' => 'Status pengaduan berhasil diperbarui', 'success' => true, 'data' => $pengaduan], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memperbarui status. ' . $e->getMessage()], 500);
        }
    }
    
}
