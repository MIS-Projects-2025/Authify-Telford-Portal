<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Card;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // Add this
use Inertia\Inertia;

class PortalController extends Controller
{
    // Cache duration in seconds (3600 = 1 hour)
    protected $cacheTime = 3600;

    /**
     * Main portal page - renders the SystemCards component
     */
    public function index(Request $request)
    {
        // Cache the sidebar departments
        $departments = Cache::remember('portal_sidebar_depts', $this->cacheTime, function () {
            return Department::where('is_active', 1)
                ->orderBy('sort_order')
                ->get();
        });

        return Inertia::render('SystemCards', [
            'departments' => $departments,
        ]);
    }

    /**
     * API: Get all departments
     */
    public function departments()
    {
        return response()->json(
            Cache::remember('api_all_departments', $this->cacheTime, function () {
                return Department::where('is_active', 1)
                    ->orderBy('sort_order')
                    ->get();
            })
        );
    }

    /**
     * API: Get cards for a specific department
     */
    public function cards($basename)
    {
        // Use the basename in the key to keep caches separate per department
        return response()->json(
            Cache::remember("api_dept_cards_{$basename}", $this->cacheTime, function () use ($basename) {
                $dept = Department::where('basename', $basename)->firstOrFail();

                return Card::where('department_id', $dept->id)
                    ->where('is_active', 1)
                    ->orderBy('sort_order')
                    ->get();
            })
        );
    }

    /**
     * API: Get systems for a specific card
     */
    public function systems($cardId)
    {
        return response()->json(
            Cache::remember("api_card_systems_{$cardId}", $this->cacheTime, function () use ($cardId) {
                return System::where('card_id', $cardId)
                    ->orderBy('sort_order')
                    ->get();
            })
        );
    }
}
