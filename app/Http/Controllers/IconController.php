<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use Illuminate\Http\Request;

class IconController extends Controller
{
    /**
     * Display a listing of the icons.
     */
    public function index()
    {
        $icons = Icon::orderBy('name', 'asc')->get();
        return view('icons', compact('icons'));
    }

    /**
     * Store a newly created icon in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:icons,name',
            'class' => 'required|string|max:255|unique:icons,class|regex:/^bi-[a-z0-9-]+$/',
        ], [
            'class.regex' => 'The icon class must be a valid Bootstrap Icon class starting with "bi-".',
        ]);

        $icon = Icon::create([
            'name' => $request->name,
            'class' => $request->class,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Icon configuration created successfully.',
            'icon' => $icon,
        ]);
    }

    /**
     * Update the specified icon in storage.
     */
    public function update(Request $request, Icon $icon)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:icons,name,' . $icon->id,
            'class' => 'required|string|max:255|regex:/^bi-[a-z0-9-]+$/|unique:icons,class,' . $icon->id,
        ], [
            'class.regex' => 'The icon class must be a valid Bootstrap Icon class starting with "bi-".',
        ]);

        $icon->update([
            'name' => $request->name,
            'class' => $request->class,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Icon details updated successfully.',
            'icon' => $icon,
        ]);
    }

    /**
     * Remove the specified icon from storage.
     */
    public function destroy(Icon $icon)
    {
        $icon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Icon configurations deleted successfully.',
        ]);
    }
}
