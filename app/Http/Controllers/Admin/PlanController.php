<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return view('admin.plans.index', ['plans' => Plan::latest()->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'data_limit_mb' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'validity_days' => 'required|integer|min:1',
            'mikrotik_profile' => 'required|string|max:100',
            'shared_users' => 'required|integer|min:1',
        ]);

        Plan::create($data);

        return back()->with('success', 'Plan created.');
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'data_limit_mb' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'validity_days' => 'required|integer|min:1',
            'mikrotik_profile' => 'required|string|max:100',
            'shared_users' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $plan->update($data);

        return back()->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        $plan->update(['is_active' => false]);

        return back()->with('success', 'Plan disabled.');
    }

    public function enable(Plan $plan)
    {
        $plan->update(['is_active' => true]);

        return back()->with('success', 'Plan enabled.');
    }

    public function forceDelete(Plan $plan)
    {
        if ($plan->payments()->exists()) {
            return back()->with('error', 'Cannot delete this plan — it has payment history. Disable it instead.');
        }

        $plan->delete();

        return back()->with('success', 'Plan deleted.');
    }
}