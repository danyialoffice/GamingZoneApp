<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display public landing page
     */
    public function index()
    {
        $tenants = Tenant::where('status', 'active')
                        ->orderBy('name')
                        ->get();

        return view('website.home', compact('tenants'));
    }

    /**
     * Select tenant/zoning
     */
    public function selectZone(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id'
        ]);

        $tenant = Tenant::find($request->tenant_id);

        if (!$tenant || $tenant->status !== 'active') {
            return redirect()->back()->with('error', 'Invalid gaming zone selected');
        }

        // Set tenant in session
        session(['tenant_id' => $tenant->id]);
        Tenant::setCurrent($tenant);

        return redirect()->route('website.index', $tenant->slug);
    }

    /**
     * Display tenant's public website
     */
    public function website(string $slug)
    {
        $tenant = Tenant::where('slug', $slug)
                       ->where('status', 'active')
                       ->firstOrFail();

        Tenant::setCurrent($tenant);
        session(['tenant_id' => $tenant->id]);

        $rooms = $tenant->rooms()->active()->get();
        $packages = $tenant->packages()->active()->get();

        return view('website.zone', compact('tenant', 'rooms', 'packages'));
    }
}
