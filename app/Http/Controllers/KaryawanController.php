<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;

class KaryawanController extends Controller
{
    // GET: Ambil semua karyawan
    public function index()
    {
        $karyawan = Karyawan::all();
        return response()->json($karyawan);
    }

    // POST: Tambah karyawan baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
        ]);

        $karyawan = Karyawan::create($validatedData);
        return response()->json(['message' => 'Karyawan berhasil ditambahkan', 'data' => $karyawan], 201);
    }

    // GET: Ambil data karyawan berdasarkan ID
    public function show($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        return response()->json($karyawan);
    }

    // PUT: Update seluruh data karyawan berdasarkan ID
    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $validatedData = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'jabatan' => 'sometimes|string|max:255',
            'gaji' => 'sometimes|numeric|min:0',
        ]);

        $karyawan->update($validatedData);

        return response()->json(['message' => 'Data berhasil diperbarui', 'data' => $karyawan]);
    }

    // PATCH: Update sebagian data karyawan (partial update)
    public function updatePartial(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $karyawan->update($request->only(['nama', 'jabatan', 'gaji']));

        $karyawan->refresh();

        return response()->json(['message' => 'Data berhasil diperbarui sebagian', 'data' => $karyawan]);
    }

    // DELETE: Hapus karyawan berdasarkan ID
    public function destroy($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        $karyawan->delete();

        return response()->json(['message' => 'Karyawan berhasil dihapus']);
    }

    // GET: Ambil absensi berdasarkan ID karyawan
    public function getAbsensi($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        return response()->json($karyawan->absensi);
    }

    // GET: Ambil gaji berdasarkan ID karyawan
    public function getGaji($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        return response()->json($karyawan->gaji);
    }

    // GET: Ambil riwayat pembayaran berdasarkan ID karyawan
    public function getPembayaran($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        return response()->json($karyawan->pembayaran);
    }
}