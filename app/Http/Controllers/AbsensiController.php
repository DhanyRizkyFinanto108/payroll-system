<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    public function index()
    {
        return response()->json(Absensi::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_absensi' => 'required|string|max:255|unique:absensi,id_absensi',
            'id_karyawan' => 'required|string|exists:karyawan,id_karyawan',
            'waktu' => 'required|date',
            'keteranx`n' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $newAbsensi = Absensi::create($request->all());
        return response()->json($newAbsensi, 201);
    }

    public function show($id)
    {
        return response()->json(Absensi::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $absensi = Absensi::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'id_karyawan' => 'string|exists:karyawan,id_karyawan',
                'waktu' => 'date',
                'keterangan' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $absensi->update($request->only(['id_karyawan', 'waktu', 'keterangan']));

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data absensi berhasil diupdate', 'data' => $absensi]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $absensi = Absensi::findOrFail($id);
            $absensi->delete();

            return response()->json(['status' => 'success', 'message' => 'Absensi berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan', 'error' => $e->getMessage()], 500);
        }
    }
}