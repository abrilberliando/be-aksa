<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    // Tugas 3: Get All Employees (Filter & Pagination)
    public function index(Request $request)
    {
        $name = $request->query('name');
        $division_id = $request->query('division_id');

        $employees = Employee::with('division') // Eager load relasi divisi
            ->when($name, fn($q) => $q->where('name', 'like', "%$name%"))
            ->when($division_id, fn($q) => $q->where('division_id', $division_id))
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Data employees fetched',
            'data' => [
                'employees' => $employees->map(fn($emp) => [
                    'id' => $emp->id,
                    'image' => url(Storage::url($emp->image)),
                    'name' => $emp->name,
                    'phone' => $emp->phone,
                    'division' => [
                        'id' => $emp->division->id,
                        'name' => $emp->division->name,
                    ],
                    'position' => $emp->position,
                ]),
            ],
            'pagination' => $employees->toArray()['links'] // Atau sesuaikan key pagination manual
        ]);
    }

    // Tugas 4: Create Employee
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string',
            'phone' => 'required|string',
            'division' => 'required|exists:divisions,id',
            'position' => 'required|string',
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);

        $path = $request->file('image')->store('employees', 'public');

        Employee::create([
            'image' => $path,
            'name' => $request->name,
            'phone' => $request->phone,
            'division_id' => $request->division,
            'position' => $request->position,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Employee created successfully']);
    }

    // Tugas 5: Update Employee
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string',
            'phone' => 'required|string',
            'division' => 'required|exists:divisions,id',
            'position' => 'required|string',
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'division_id' => $request->division,
            'position' => $request->position,
        ];

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($employee->image);
            $data['image'] = $request->file('image')->store('employees', 'public');
        }

        $employee->update($data);

        return response()->json(['status' => 'success', 'message' => 'Employee updated successfully']);
    }

    // Tugas 6: Delete Employee
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        Storage::disk('public')->delete($employee->image);
        $employee->delete();

        return response()->json(['status' => 'success', 'message' => 'Employee deleted successfully']);
    }
}
