<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PC;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PCController extends Controller
{
    /**
     * Display list of PCs
     */
    public function index(Request $request)
    {
        $query = PC::with('room');

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $pcs = $query->orderBy('name')->paginate(20);
        $rooms = Room::orderBy('name')->get();

        return view('tenant.pcs.index', compact('pcs', 'rooms'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $rooms = Room::where('status', 'active')->orderBy('name')->get();
        
        return view('tenant.pcs.create', compact('rooms'));
    }

    /**
     * Store new PC
     */
    public function store(Request $request)
    {
        $tenant = Tenant::current();

        // Check limit
        if (!$tenant->canAddPC()) {
            return back()->with('error', 'PC limit reached for your subscription plan');
        }

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'specs' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|string',
            'status' => 'required|in:available,maintenance,offline',
        ]);

        PC::create($request->only([
            'room_id', 'name', 'specs', 'hourly_rate', 
            'ip_address', 'mac_address', 'status'
        ]));

        return redirect()->route('tenant.pcs.index')
                       ->with('success', 'PC created successfully');
    }

    /**
     * Display PC details
     */
    public function show(PC $pc)
    {
        $pc->load(['room', 'bookings' => function ($query) {
            $query->where('start_time', '>=', now()->subDays(7))
                  ->orderBy('start_time', 'desc');
        }]);

        return view('tenant.pcs.show', compact('pc'));
    }

    /**
     * Show edit form
     */
    public function edit(PC $pc)
    {
        $rooms = Room::where('status', 'active')->orderBy('name')->get();
        
        return view('tenant.pcs.edit', compact('pc', 'rooms'));
    }

    /**
     * Update PC
     */
    public function update(Request $request, PC $pc)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'specs' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|string',
            'status' => 'required|in:available,occupied,maintenance,offline',
        ]);

        $pc->update($request->only([
            'room_id', 'name', 'specs', 'hourly_rate',
            'ip_address', 'mac_address', 'status'
        ]));

        return redirect()->route('tenant.pcs.show', $pc->id)
                       ->with('success', 'PC updated successfully');
    }

    /**
     * Delete PC
     */
    public function destroy(PC $pc)
    {
        // Check if PC has active bookings
        $activeBooking = $pc->bookings()
                           ->whereIn('status', ['pending', 'confirmed'])
                           ->exists();

        if ($activeBooking) {
            return back()->with('error', 'Cannot delete PC with active bookings');
        }

        $pc->delete();

        return redirect()->route('tenant.pcs.index')
                       ->with('success', 'PC deleted successfully');
    }
}
