<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use Exception;
use Illuminate\Validation\ValidationException;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Division::query();

            if ($request->has('name') && $request->name !== null) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            $divisi = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'message' => 'Data divisi berhasil diambil',
                'data' => [
                    'divisions' => $divisi->items(),
                ],
                'pagination' => [
                    'current_page' => $divisi->currentPage(),
                    'last_page' => $divisi->lastPage(),
                    'per_page' => $divisi->perPage(),
                    'total' => $divisi->total(),
                    'from' => $divisi->firstItem(),
                    'to' => $divisi->lastItem(),
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal' + $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request) 
    {
        try {
            $request->validate([
                'name' => 'required|max:200'
            ]);

            
        } catch(ValidationException $e){

        } catch(Exception $e){

        }
    } 
}
