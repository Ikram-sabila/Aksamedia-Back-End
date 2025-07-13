<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\{Karyawan, Division};
use App\Http\Resources\KaryawanResource;
use Illuminate\Validation\ValidationException;
use Exception;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Karyawan::with('division');

            if ($request->has('name') && $request->name !== null) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->has('division_id') && $request->division_id !== null) {
                $query->where('division_id', $request->division_id);
            }

            $karyawans = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'message' => 'Data karyawan berhasil diambil',
                'data' => [
                    'employees' => KaryawanResource::collection($karyawans->items()),
                ],
                'pagination' => [
                    'current_page' => $karyawans->currentPage(),
                    'last_page' => $karyawans->lastPage(),
                    'per_page' => $karyawans->perPage(),
                    'total' => $karyawans->total(),
                    'from' => $karyawans->firstItem(),
                    'to' => $karyawans->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data karyawan: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info($request->all());

            $request->validate([
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'division_id' => 'required|exists:divisions,id',
                'position' => 'required|string|max:100'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('karyawan', 'public');
            }

            
            Karyawan::create([
                'id' => 'KRY' . $request->division_id . strtoupper(Str::random(4)),
                'image' => $imagePath ? Storage::url($imagePath) : null,
                'name' => $request->name,
                'phone' => $request->phone,
                'division_id' => $request->division_id,
                'position' => $request->position,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Karyawan berhasil ditambahkan',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'error' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $karyawan = Karyawan::with('division')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diambil',
                'employee' => $karyawan
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan',
                'error' => $e->errors()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {            
            $request->validate([
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'division_id' => 'required|exists:divisions,id',
                'position' => 'required|string|max:100'
            ]);

            $karyawan = Karyawan::findOrFail($id);

            $imagePath = $karyawan->image;
            if ($request->hasFile('image')) {
                if ($karyawan->image && Storage::exists(str_replace('/storage/', 'public/', $karyawan->image))) {
                    Storage::delete(str_replace('/storage/', 'public/', $karyawan->image));
                }

                $path = $request->file('image')->store('karyawan', 'public');
                $imagePath = Storage::url($path);
            }

            $karyawan->update([
                'image' => $imagePath,
                'name' => $request->name,
                'phone' => $request->phone,
                'division_id' => $request->division_id,
                'position' => $request->position
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Karyawan berhasil diperbarui'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal ',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat update data'.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);

            if ($karyawan->image && Storage::exists(str_replace('/storage/', '/public/', $karyawan->image))) {
                Storage::delete(str_replace('/storage/', '/public/', $karyawan->image));
            }

            $karyawan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Karyawan berhasil dihapus'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan ' . $e->errors()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan',
                'error' => $e->getMessage()
            ]);
        }
    }
}
