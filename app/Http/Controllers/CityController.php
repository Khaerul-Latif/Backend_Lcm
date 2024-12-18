<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        return response()->json(City::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kota' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        $city = City::create($validated);

        return response()->json($city, 201);
    }

    public function show(City $city)
    {
        return response()->json($city);
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'kode_kota' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        $city->update($validated);

        return response()->json($city);
    }

    public function destroy(City $city)
    {
        $city->delete();

        return response()->json(['message' => 'City deleted']);
    }
}
