<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display list of rooms
     */
    public function index()
    {
        $rooms = Room::withCount('pcs')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->paginate(20);

        return view('tenant.rooms.index', compact('rooms'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('tenant.rooms.create');
    }

    /**
     * Store new room
     */
    public function store(Request $request)
    {
        $tenant = Tenant::current();

        // Check limit
        if (!$tenant->canAddRoom()) {
            return back()->with('error', 'Room limit reached for your subscription plan');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->only(['name', 'description', 'hourly_rate', 'status', 'sort_order']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('rooms', 'public');
            $data['image'] = $path;
        }

        Room::create($data);

        return redirect()->route('tenant.rooms.index')
                       ->with('success', 'Room created successfully');
    }

    /**
     * Display room details
     */
    public function show(Room $room)
    {
        $room->load('pcs');

        return view('tenant.rooms.show', compact('room'));
    }

    /**
     * Show edit form
     */
    public function edit(Room $room)
    {
        return view('tenant.rooms.edit', compact('room'));
    }

    /**
     * Update room
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->only(['name', 'description', 'hourly_rate', 'status', 'sort_order']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('rooms', 'public');
            $data['image'] = $path;
        }

        $room->update($data);

        return redirect()->route('tenant.rooms.show', $room->id)
                       ->with('success', 'Room updated successfully');
    }

    /**
     * Delete room
     */
    public function destroy(Room $room)
    {
        // Check if room has bookings
        if ($room->bookings()->exists()) {
            return back()->with('error', 'Cannot delete room with existing bookings');
        }

        $room->delete();

        return redirect()->route('tenant.rooms.index')
                       ->with('success', 'Room deleted successfully');
    }
}
