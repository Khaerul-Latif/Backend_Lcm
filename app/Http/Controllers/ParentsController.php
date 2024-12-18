<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use Illuminate\Http\Request;

class ParentsController extends Controller
{
    public function index()
    {
        $parents = Parent::with('user')->get();
        return response()->json($parents);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_parent' => 'required|integer',
            'name' => 'required|string',
            'email' => 'nullable|email|unique:parents',
            'phone' => 'nullable|string|unique:parents',
            'jobs' => 'nullable|string',
            'address' => 'nullable|string',
            'is_father' => 'nullable|integer',
            'is_mother' => 'nullable|integer',
        ]);

        $parent = Parent::create($request->all());

        return response()->json(['message' => 'Parent created successfully', 'parent' => $parent], 201);
    }

    public function show($id)
    {
        $parent = Parent::with('user')->find($id);

        if (!$parent) {
            return response()->json(['message' => 'Parent not found'], 404);
        }

        return response()->json($parent);
    }

    public function update(Request $request, $id)
    {
        $parent = Parent::find($id);

        if (!$parent) {
            return response()->json(['message' => 'Parent not found'], 404);
        }

        $request->validate([
            'id_parent' => 'sometimes|required|integer',
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|nullable|email|unique:parents,email,' . $id,
            'phone' => 'sometimes|nullable|string|unique:parents,phone,' . $id,
            'jobs' => 'sometimes|nullable|string',
            'address' => 'sometimes|nullable|string',
            'is_father' => 'sometimes|nullable|integer',
            'is_mother' => 'sometimes|nullable|integer',
        ]);

        $parent->update($request->all());

        return response()->json(['message' => 'Parent updated successfully', 'parent' => $parent]);
    }

    public function destroy($id)
    {
        $parent = Parent::find($id);

        if (!$parent) {
            return response()->json(['message' => 'Parent not found'], 404);
        }

        $parent->delete();

        return response()->json(['message' => 'Parent deleted successfully']);
    }
}
