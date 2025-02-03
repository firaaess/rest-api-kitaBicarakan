<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class KategoriControllers extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        return response()->json(['data' => $kategoris, 'success' => true], 200);
    }
    public function addKategori(Request $request)
    {
        // Periksa apakah pengguna memiliki peran admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'hanya admin yang bisa menambahkan kategori'], 403);
        }

        $validated = Validator::make($request->all(), [
            'nama' => 'required|string|unique:kategoris|max:255',
        ]);

        if ($validated->fails()) {
            return response()->json(['error' => $validated->errors()], 400);
        }

        try {

        $user = Kategori::create([
            'nama' => $request->nama,
        ]);

        return response()->json(['message' => 'kategori berhasil ditambahkan.', 'success' => true], 201);
    }
    catch (\Exception $e) {
        return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data. '. $e->getMessage()], 500);
    }
}
public function deleteKategoriById($id)
    {
        try {
            // Cari kategori berdasarkan ID
            $kategori = Kategori::find($id);

            // Jika kategori tidak ditemukan
            if (!$kategori) {
                return response()->json(['message' => 'Kategori tidak ditemukan', 'success' => false], 404);
            }

            // Hapus user
            $kategori->delete();

            return response()->json([
                'message' => 'Kategori berhasil dihapus.',
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
