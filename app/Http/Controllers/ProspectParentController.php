<?php

namespace App\Http\Controllers;

use App\Models\ProspectParent;
use Illuminate\Http\Request;

class ProspectParentController extends Controller
{
    public function index()
    {
        $prospects = ProspectParent::with(['city', 'program'])->get();

        return response()->json($prospects, 200);
    }

    public function show(ProspectParent $prospectParent)
    {
        $prospect = $prospectParent->load(['city', 'program']);

        return response()->json($prospect, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:prospect_parents,email',
            'phone' => 'nullable|numeric',
            'source' => 'nullable|string|max:255',
            'id_city' => 'nullable|exists:citys,id',
            'id_program' => 'nullable|exists:programs,id',
            'invoice_sp' => 'nullable|exists:payment_sp,id',
            'call' => 'nullable|integer|min:0',
            'tgl_checkin' => 'nullable|date',
            'invitional_code' => 'nullable|string|max:255',
        ]);

    //     $isDuplicate = ProspectParent::where('phone', $validated['phone'])
    //     ->where('email', $validated['email'])
    //     ->where('id_program', $validated['id_program'])
    //     ->where('id_city', $validated['id_city'])
    //     ->exists();

    // if ($isDuplicate) {
    //     return response()->json([
    //         'error' => 'Duplicate Entry',
    //         'message' => 'Phone and email are already registered for the same program and city.'
    //     ], 422);
    // }0


        $prospect = ProspectParent::create($validated);

        return response()->json($prospect, 201);
    }

    public function update(Request $request, ProspectParent $prospectParent)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:prospect_parents,email,' . $prospectParent->id,
            'phone' => 'nullable|numeric',
            'source' => 'nullable|string|max:255',
            'id_city' => 'nullable|exists:citys,id',
            'id_program' => 'nullable|exists:programs,id',
            'invoice_sp' => 'nullable|exists:payment_sp,id',
            'call' => 'nullable|integer|min:0',
            'tgl_checkin' => 'nullable|date',
            'invitional_code' => 'nullable|string|max:255',
        ]);

        $prospectParent->update($validated);

        return response()->json($prospectParent, 200);
    }

    public function destroy(ProspectParent $prospectParent)
    {
        $prospectParent->delete();

        return response()->json(['message' => 'Prospect parent deleted successfully'], 200);
    }
}
