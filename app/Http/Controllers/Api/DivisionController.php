<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        try {
            // 1. Ambil query pencarian dari request (Bisa dari query param atau body)
            $name = $request->query('name') ?? $request->input('name');

            // 2. Query Builder dengan filter & pagination
            $divisions = Division::query()
                ->when($name, function ($query, $name) {
                    return $query->where('name', 'like', '%' . $name . '%');
                })
                ->paginate(10); // Kita set per halaman 10 data

            // 3. Response format sesuai permintaan soal
            return response()->json([
                'status' => 'success',
                'message' => 'Data divisions fetched successfully',
                'data' => [
                    'divisions' => $divisions->map(function ($division) {
                        return [
                            'id' => $division->id,
                            'name' => $division->name,
                        ];
                    }),
                ],
                'pagination' => [
                    'current_page' => $divisions->currentPage(),
                    'last_page' => $divisions->lastPage(),
                    'per_page' => $divisions->perPage(),
                    'total' => $divisions->total(),
                    'next_page_url' => $divisions->nextPageUrl(),
                    'prev_page_url' => $divisions->previousPageUrl(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
