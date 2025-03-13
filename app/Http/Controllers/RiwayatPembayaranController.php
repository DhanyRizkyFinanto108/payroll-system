<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatPembayaran;
use App\Models\GajiBulanan;
use Illuminate\Support\Facades\Validator;

class RiwayatPembayaranController extends Controller
{
    public function index()
    {
        try {
            $pembayaran = RiwayatPembayaran::all();
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi request
            $validator = Validator::make($request->all(), [
                'id_pembayaran' => 'required|string|unique:riwayat_pembayaran,id_pembayaran',
                'waktu' => 'required|date',
                'metode' => 'required|string|in:Transfer Bank,Tunai,E-wallet',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $newPembayaran = RiwayatPembayaran::create($request->all());
            return response()->json($newPembayaran, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);

            // Validasi request untuk update
            $validator = Validator::make($request->all(), [
                'waktu' => 'date',
                'metode' => 'string|in:Transfer Bank,Tunai,E-wallet,QRIS',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $pembayaran->update($request->all());
            return response()->json($pembayaran);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = RiwayatPembayaran::destroy($id);
            return response()->json(['success' => $deleted > 0]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getGajiBulanan($id)
    {
        try {
            $pembayaran = RiwayatPembayaran::findOrFail($id);
            // Ambil data Gaji_bulanan yang terkait dengan RiwayatPembayaran ini
            $gaji = GajiBulanan::where('id_pembayaran', $id)->get();
            return response()->json($gaji);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}