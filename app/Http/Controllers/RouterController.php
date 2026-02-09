<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Config;
use Exception;

class RouterController extends Controller
{
    public function index() {
        $routers = Router::where('user_id', auth()->id())->get();
        return view('routers.index', compact('routers'));
    }

    public function create() {
        return view('routers.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'local_ip' => 'required|ip',
            'api_user' => 'required|string',
            'api_password' => 'required|string',
        ]);

        $validated['vpn_ip'] = '10.0.0.' . (Router::count() + 2);
        $validated['user_id'] = auth()->id(); 

        Router::create($validated);

        return redirect()->route('routers.index')->with('alert_success', 'Router added to Passtik!');
    }

    public function show(Router $router) {
        return view('routers.show', compact('router'));
    }

    public function edit(Router $router) {
        return view('routers.edit', compact('router'));
    }

    public function update(Request $request, Router $router) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'local_ip' => 'required|ip',
            'api_user' => 'required|string',
            'api_password' => 'required|string',
        ]);

        $router->update($validated);

        return redirect()->route('routers.index')->with('alert_success', 'Router updated successfully!');
    }

    public function destroy(Router $router) {
        $router->delete();
        return redirect()->route('routers.index')->with('alert_success', 'Router deleted successfully!');
    }

    public function checkStatus(Router $router) {
        $config = new Config([
            'host' => $router->local_ip,
            'user' => $router->api_user,
            'pass' => $router->api_password,
            'port' => 8728,
        ]);

        try {
            $client = new Client($config);
            $responses = $client->query('/system/identity/print')->read();
            $routerName = $responses[0]['name'] ?? 'Unknown';
            
            return back()->with('alert_success', "Connected to: {$routerName}");
        } catch (Exception $e) {
            return back()->with('alert_error', "Connection failed: " . $e->getMessage());
        }
    }

    public function manage(Router $router) {
        $config = new Config([
            'host' => $router->local_ip,
            'user' => $router->api_user,
            'pass' => $router->api_password,
            'port' => 8728,
            'timeout' => 3,
        ]);

        try {
            $client = new Client($config);
            $resourceData = $client->query('/system/resource/print')->read();
            $identityData = $client->query('/system/identity/print')->read();

            return view('routers.manage', [
                'router' => $router,
                'info' => !empty($resourceData) ? $resourceData[0] : null,
                'name' => !empty($identityData) ? $identityData[0]['name'] : $router->name
            ]);
        } catch (Exception $e) {
            return view('routers.manage', [
                'router' => $router,
                'info' => null,
                'name' => $router->name,
                'connectionError' => $e->getMessage()
            ]);
        }
    }

    public function clearVouchers(Router $router) {
        $config = new Config([
            'host' => $router->local_ip,
            'user' => $router->api_user,
            'pass' => $router->api_password,
            'port' => 8728,
            'timeout' => 5,
        ]);

        try {
            $client = new Client($config);
            
            $users = $client->query('/ip/hotspot/user/print')->read();
            $deletedCount = 0;
            
            foreach ($users as $user) {
                if (isset($user['.id'])) {
                    $client->query([
                        '/ip/hotspot/user/remove',
                        '=.id=' . $user['.id']
                    ])->read();
                    $deletedCount++;
                }
            }
            
            $dbDeleted = $router->vouchers()->delete();
            
            return redirect()->route('routers.index')
                ->with('alert_success', "Cleared {$deletedCount} vouchers from device and {$dbDeleted} from database");
        } catch (Exception $e) {
            return redirect()->route('routers.index')
                ->with('alert_error', 'Failed to clear vouchers: ' . $e->getMessage());
        }
    }

    public function networkTraffic(Router $router) {
        $cacheKey = 'traffic_' . $router->id . '_' . request()->ip();
        if (cache()->has($cacheKey)) {
            return response()->json(['success' => false, 'error' => 'Rate limited']);
        }
        cache()->put($cacheKey, true, 2);
        
        $config = new Config([
            'host' => $router->local_ip,
            'user' => $router->api_user,
            'pass' => $router->api_password,
            'port' => 8728,
            'timeout' => 3,
        ]);

        try {
            $client = new Client($config);
            $interfaces = $client->query('/interface/print')->read();
            
            $trafficData = [];
            foreach ($interfaces as $interface) {
                if (isset($interface['name']) && !str_contains($interface['name'], 'lo')) {
                    $stats = $client->query([
                        '/interface/monitor-traffic',
                        '=interface=' . $interface['name'],
                        '=duration=1'
                    ])->read();
                    
                    if (!empty($stats)) {
                        $trafficData[] = [
                            'interface' => $interface['name'],
                            'rx_bits_per_second' => $stats[0]['rx-bits-per-second'] ?? 0,
                            'tx_bits_per_second' => $stats[0]['tx-bits-per-second'] ?? 0,
                        ];
                    }
                }
            }
            
            return response()->json(['success' => true, 'data' => $trafficData]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Connection failed']);
        }
    }
}
