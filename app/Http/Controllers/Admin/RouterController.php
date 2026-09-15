<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index()
    {
        return view('admin.routers.index', ['routers' => Router::latest()->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'ip_address' => 'required|ip',
            'api_port' => 'required|integer',
            'use_ssl' => 'boolean',
            'username' => 'required|string',
            'password' => 'required|string',
            'hotspot_server' => 'required|string',
            'default_profile' => 'required|string',
        ]);

        Router::create($data); // password gets encrypted via the model mutator

        return back()->with('success', 'Router added.');
    }

    /** Quick connectivity test button in the admin UI. */
    public function testConnection(Router $router)
    {
        try {
            $mikrotik = new MikrotikService($router);
            $mikrotik->listActiveSessions();
            $router->update(['last_connected_at' => now()]);

            return back()->with('success', "Connected to {$router->name} successfully.");
        } catch (\Throwable $e) {
            return back()->with('error', "Could not connect to {$router->name}: {$e->getMessage()}");
        }
    }

    public function activeSessions(Router $router)
    {
        $mikrotik = new MikrotikService($router);
        $sessions = $mikrotik->listActiveSessions();

        return view('admin.routers.sessions', compact('router', 'sessions'));
    }

    public function kick(Router $router, string $voucherCode)
    {
        $mikrotik = new MikrotikService($router);
        $mikrotik->kickActiveSession($voucherCode);

        return back()->with('success', "Disconnected {$voucherCode}.");
    }
}
