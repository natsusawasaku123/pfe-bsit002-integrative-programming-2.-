<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'sometimes|nullable|string|max:100',
            'department_id' =>
                'sometimes|nullable|integer|exists:departments,id',
            'page' => 'sometimes|integer|min:1',
        ]);

        $query = Employee::with('department');

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where(
                'department_id',
                $request->input('department_id')
            );
        }

        return response()->json(
            $query->orderBy('id')
                ->paginate(10)
                ->withQueryString()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:employees,email',
            'department_id' => 'required|integer|exists:departments,id',
            'position' => 'required|string|max:100',
        ]);

        $employee = Employee::create($validated);

        return response()->json(
            $employee->load('department'),
            201
        );
    }

    public function show($id)
    {
        $employee = Employee::with('department')->find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee),
            ],
            'department_id' =>
                'sometimes|required|integer|exists:departments,id',
            'position' => 'sometimes|string|max:100',
        ]);

        $employee->update($validated);

        return response()->json(
            $employee->load('department')
        );
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully',
        ]);
    }
}