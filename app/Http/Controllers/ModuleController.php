<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ModuleController extends Controller
{
    /**
     * Display the dynamic modules configuration interface.
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Access Denied: You must be an administrator.');
        }

        // Get modules ordered hierarchically
        $roots = Module::whereNull('parent_id')->orderBy('order_weight')->with('children')->get();
        $flatModules = collect();
        foreach ($roots as $root) {
            $flatModules->push($root);
            foreach ($root->children as $child) {
                $flatModules->push($child);
            }
        }

        // Available parents (only root-level items can be parents to keep it 2 levels deep)
        $parentModules = Module::whereNull('parent_id')->orderBy('order_weight')->get();

        return view('modules', compact('roots', 'flatModules', 'parentModules'));
    }

    /**
     * Store a newly created module.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:modules,id',
            'icon_class' => 'nullable|string|max:255',
            'is_visible' => 'nullable|boolean',
        ]);

        try {
            $parentId = $request->parent_id ?: null;
            $maxWeight = Module::where('parent_id', $parentId)->max('order_weight');

            $module = Module::create([
                'title' => $request->title,
                'url' => $request->url,
                'parent_id' => $parentId,
                'icon_class' => $request->icon_class,
                'is_visible' => $request->has('is_visible') ? (bool) $request->is_visible : true,
                'order_weight' => ($maxWeight !== null) ? $maxWeight + 1 : 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Module '" . $module->title . "' created successfully!",
                'module' => $module
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create module: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save module details.'], 500);
        }
    }

    /**
     * Update an existing module.
     */
    public function update(Request $request, Module $module)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:modules,id',
            'icon_class' => 'nullable|string|max:255',
        ]);

        try {
            $newParentId = $request->parent_id ?: null;

            // Prevent assigning self as parent
            if ($newParentId && (int)$newParentId === (int)$module->id) {
                return response()->json(['success' => false, 'message' => 'A module cannot be its own parent.'], 400);
            }

            $module->update([
                'title' => $request->title,
                'url' => $request->url,
                'parent_id' => $newParentId,
                'icon_class' => $request->icon_class,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Module updated successfully!',
                'module' => $module
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update module: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update module details.'], 500);
        }
    }

    /**
     * Toggle visibility of a module.
     */
    public function toggle(Request $request, Module $module)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        try {
            $module->update([
                'is_visible' => !$module->is_visible
            ]);

            return response()->json([
                'success' => true,
                'message' => "Module visibility turned " . ($module->is_visible ? 'ON' : 'OFF') . ".",
                'is_visible' => $module->is_visible
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to toggle module status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update module status.'], 500);
        }
    }

    /**
     * Remove a module.
     */
    public function destroy(Module $module)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        try {
            $title = $module->title;
            $module->delete();

            return response()->json([
                'success' => true,
                'message' => "Module '" . $title . "' deleted successfully."
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete module: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete module.'], 500);
        }
    }

    /**
     * Update order weights of modules dynamically.
     */
    public function updateOrder(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:modules,id',
            'order.*.order_weight' => 'required|integer',
        ]);

        try {
            foreach ($request->order as $orderData) {
                Module::where('id', $orderData['id'])->update([
                    'order_weight' => $orderData['order_weight']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Modules sorted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sort modules: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update sorting order.'], 500);
        }
    }
}
