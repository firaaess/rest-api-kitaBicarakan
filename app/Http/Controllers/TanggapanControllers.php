<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tanggapan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TanggapanControllers extends Controller
{
    public function index()
    {
        $tanggapan = Tanggapan::all();
        return response()->json(['data' => $tanggapan, 'success' => true], 200);    }

    public function addTanggapan(Request $request, $pengaduan_id)
    {
        // Periksa apakah pengguna memiliki peran petugas
        if (Auth::user()->role !== 'petugas') {
            return response()->json(['error' => 'hanya petugas yang bisa memberikan tanggapan'], 403);
        }

        $validated = Validator::make($request->all(),[
            'tanggapan' => 'required|string',
            'foto' => 'required|image|mimes:jpg,png,jpg|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json([$validated->errors()], 403);
        }

           // Periksa apakah tanggapan untuk pengaduan ini sudah ada
        $existingTanggapan = Tanggapan::where('pengaduan_id', $pengaduan_id)->first();
        if ($existingTanggapan) {
            return response()->json(['message' => 'Tanggapan untuk pengaduan ini sudah ada'], 409); // 409: Conflict
        }

        try {
            // Upload foto ke Cloudinary
            $image = $request->file('foto');
            $uploadResult = Cloudinary::upload($image->getRealPath(), [
                'folder' => 'tanggapan_foto',  // Folder di Cloudinary
            ]);

            // Mendapatkan URL gambar setelah diupload
            $fotoUrl = $uploadResult->getSecurePath();

            // Menyimpan data tanggapan ke database
            $tanggapan = Tanggapan::create([
                'pengaduan_id' => $pengaduan_id,
                'user_id' => auth()->user()->id,
                'tanggapan' => $request->tanggapan,
                'foto' => $fotoUrl, // Menyimpan URL foto dari Cloudinary
            ]);

            return response()->json(['message' => 'Tanggapan berhasil dikirim', 'data' => $tanggapan, 'success' => true], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data. ' . $e->getMessage()], 500);
        }
    }

    public function getTanggapanByPengaduanId($pengaduan_id)
{
    try {
        // Ambil tanggapan berdasarkan pengaduan_id
        $tanggapan = Tanggapan::where('pengaduan_id', $pengaduan_id)->with('user')->get();

        // Periksa apakah ada tanggapan
        if ($tanggapan->isEmpty()) {
            return response()->json(['message' => 'Tidak ada tanggapan untuk pengaduan ini', 'success' => false], 404);
        }

        return response()->json(['data' => $tanggapan, 'success' => true], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
    }
}

}
