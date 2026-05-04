<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CardController extends Controller
{
    /**
     * Helper to clear specific department card cache
     */
    protected function clearCardCache($departmentId)
    {
        // Find the department to get the basename
        $dept = Department::find($departmentId);
        if ($dept) {
            Cache::forget("api_dept_cards_{$dept->basename}");
        }
    }
  public function index()
    {
        return response()->json(
            Card::with('department')->orderBy('sort_order')->get()
        );
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'card_icon'     => 'required|string|max:100',
            'card_title'    => 'required|string|max:150',
            'description'   => 'nullable|string',
            'sort_order'    => 'integer',
            'is_active'     => 'boolean',
        ]);

        $card = Card::create($data);
        $this->clearCardCache($card->department_id);

        return response()->json($card, 201);
    }

    public function update(Request $request, $id)
    {
        $card = Card::findOrFail($id);
        $oldDeptId = $card->department_id;

        $data = $request->validate([
            'department_id' => 'exists:departments,id',
            'card_icon'     => 'string|max:100',
            'card_title'    => 'string|max:150',
            'description'   => 'nullable|string',
            'sort_order'    => 'integer',
            'is_active'     => 'boolean',
        ]);

        $card->update($data);
        
        // Clear cache for both old and new department (if changed)
        $this->clearCardCache($oldDeptId);
        if ($oldDeptId != $card->department_id) {
            $this->clearCardCache($card->department_id);
        }

        return response()->json($card);
    }

    public function destroy($id)
    {
        $card = Card::findOrFail($id);
        $deptId = $card->department_id;
        $card->delete();
        
        $this->clearCardCache($deptId);

        return response()->json(['message' => 'Deleted']);
    }
}