<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemController extends Controller
{
    public function index()
    {
        return response()->json(
            System::with('card.department')->orderBy('sort_order')->get()
        );
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'card_id'            => 'required|exists:cards,id',
            'list_name'          => 'required|string|max:150',
            'system_url'         => 'required|string|max:500',
            'modal_icon'         => 'required|string|max:100',
            'system_status'      => 'integer|in:0,1,2',
            'require_auto_login' => 'boolean',
            'sort_order'         => 'integer',
        ]);

        $system = System::create($data);
        
        // Clear the specific system list cache for this card
        Cache::forget("api_card_systems_{$system->card_id}");

        return response()->json($system, 201);
    }

    public function update(Request $request, $id)
    {
        $system = System::findOrFail($id);
        $oldCardId = $system->card_id;

        $data = $request->validate([
            'card_id'            => 'exists:cards,id',
            'list_name'          => 'string|max:150',
            'system_url'         => 'string|max:500',
            'modal_icon'         => 'string|max:100',
            'system_status'      => 'integer|in:0,1,2',
            'require_auto_login' => 'boolean',
            'sort_order'         => 'integer',
        ]);

        $system->update($data);
        
        // Clear cache for old and new card
        Cache::forget("api_card_systems_{$oldCardId}");
        if ($oldCardId != $system->card_id) {
            Cache::forget("api_card_systems_{$system->card_id}");
        }

        return response()->json($system);
    }

    public function destroy($id)
    {
        $system = System::findOrFail($id);
        $cardId = $system->card_id;
        $system->delete();
        
        Cache::forget("api_card_systems_{$cardId}");

        return response()->json(['message' => 'Deleted']);
    }
}