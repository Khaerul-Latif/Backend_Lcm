<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('roles')->get();
        return response()->json($menus);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'link' => 'required',
            'roles' => 'array', // Data yang disimpan disini harus berupa array
            'roles.*' => 'exists:roles,id',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'link' => $request->link,
        ]);

        if ($request->has('roles')) {
            $menu->roles()->sync($request->roles);
        }

        return response()->json(['message' => 'Menu created successfully', 'menu' => $menu], 201);
    }


    public function show($id)
    {
        $menu = Menu::with('roles')->find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required',
            'link' => 'sometimes|required',
            'roles' => 'array', // Harus berupa array
            'roles.*' => 'exists:roles,id',
        ]);

        $menu->update([
            'name' => $request->name ?? $menu->name,
            'link' => $request->link ?? $menu->link,
        ]);

        if ($request->has('roles')) {
            $menu->roles()->sync($request->roles);
        }

        return response()->json(['message' => 'Menu updated successfully', 'menu' => $menu]);
    }


    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu deleted successfully']);
    }
}
