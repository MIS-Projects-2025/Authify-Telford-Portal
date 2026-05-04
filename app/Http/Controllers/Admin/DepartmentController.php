<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // Add this

class DepartmentController extends Controller
{
    /**
     * Helper to clear portal-related caches
     */
    protected function clearPortalCache()
    {
        // Clear the sidebar and the general departments list
        Cache::forget('portal_sidebar_depts');
        Cache::forget('api_all_departments');
        
        // Note: If you have many departments, you might want to 
        // clear specific card caches too if the basename changed.
    }

    public function index()
    {
        return response()->json(
            Department::orderBy('sort_order')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'basename'   => 'required|string|max:50|unique:departments,basename',
            'color_key'  => 'required|string|max:50',
            'icon'       => 'required|string|max:100',
            'sort_order' => 'integer',
            'is_active'  => 'boolean',
        ]);

        $dept = Department::create($data);
        
        $this->clearPortalCache(); // Clear cache on create

        return response()->json($dept, 201);
    }

    public function update(Request $request, $id)
    {
        $dept = Department::findOrFail($id);
        $oldBasename = $dept->basename; // Store old basename to clear specific card cache

        $data = $request->validate([
            'name'       => 'string|max:100',
            'basename'   => 'string|max:50|unique:departments,basename,' . $id,
            'color_key'  => 'string|max:50',
            'icon'       => 'string|max:100',
            'sort_order' => 'integer',
            'is_active'  => 'boolean',
        ]);

        $dept->update($data);
        
        $this->clearPortalCache(); // Clear general cache
        
        // Also clear the cards cache for this specific department
        Cache::forget("api_dept_cards_{$oldBasename}");
        if ($oldBasename !== $dept->basename) {
            Cache::forget("api_dept_cards_{$dept->basename}");
        }

        return response()->json($dept);
    }

    public function destroy($id)
    {
        $dept = Department::findOrFail($id);
        $basename = $dept->basename;
        
        $dept->delete();
        
        $this->clearPortalCache(); // Clear general cache
        Cache::forget("api_dept_cards_{$basename}");

        return response()->json(['message' => 'Deleted']);
    }
}