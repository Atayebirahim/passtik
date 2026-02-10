<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Config;
use App\Services\WireGuardService;
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
            'api_user' => 'required|string|max:100|regex:/^[a-zA-Z0-9_-]+$/',
            'api_password' => 'required|string|min:6|max:255',
        ]);

        try {
            $wg = new WireGuardService();
            $vpnConfig = $wg->generatePeerConfig();
            
            $validated['vpn_ip'] = $vpnConfig['peer_ip'];
            $validated['vpn_public_key'] = $vpnConfig['peer_public_key'];
            $validated['vpn_private_key'] = encrypt($vpnConfig['peer_private_key']);
            $validated['user_id'] = auth()->id();
            
            $router = Router::create($validated);
            
            $wg->addPeerToVps($vpnConfig['peer_public_key'], $vpnConfig['peer_ip']);

            return redirect()->route('routers.show', $router)->with('alert_success', 'Router added! Download setup script below.');
        } catch (Exception $e) {
            \Log::error('VPN configuration failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('alert_error', 'Failed to configure VPN. Please try again.');
        }
    }

    public function show(Router $router) {
        $this->authorize('view', $router);
        
        if (!$router->vpn_private_key) {
            return redirect()->route('routers.index')->with('alert_error', 'Router not configured for VPN. Please delete and re-add.');
        }
        
        try {
            $wg = new WireGuardService();
            $vpsPublicIp = config('app.vps_public_ip');
            
            if (!$vpsPublicIp || $vpsPublicIp === 'your.vps.ip.here') {
                return redirect()->route('routers.index')->with('alert_error', 'VPS_PUBLIC_IP not configured in .env file');
            }
            
            $config = [
                'peer_ip' => $router->vpn_ip,
                'peer_private_key' => decrypt($router->vpn_private_key),
                'vps_public_key' => $wg->getVpsPublicKey(),
                'vps_ip' => '10.0.0.1',
            ];
            
            $script = $wg->generateMikrotikScript($config, $vpsPublicIp, $router->api_password);
            $vpnStatus = $wg->checkPeerStatus($router->vpn_public_key);
            
            return view('routers.show', compact('router', 'script', 'vpnStatus'));
        } catch (Exception $e) {
            \Log::error('Script generation failed', ['router_id' => $router->id, 'error' => $e->getMessage()]);
            return redirect()->route('routers.index')->with('alert_error', 'Failed to generate script. Please try again.');
        }
    }

    public function edit(Router $router) {
        $this->authorize('update', $router);
        return view('routers.edit', compact('router'));
    }

    public function update(Request $request, Router $router) {
        $this->authorize('update', $router);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'local_ip' => 'required|ip',
            'api_user' => 'required|string|max:100|regex:/^[a-zA-Z0-9_-]+$/',
            'api_password' => 'required|string|min:6|max:255',
        ]);

        $router->update($validated);

        return redirect()->route('routers.index')->with('alert_success', 'Router updated successfully!');
    }

    public function destroy(Router $router) {
        $this->authorize('delete', $router);
        try {
            $wg = new WireGuardService();
            $wg->removePeerFromVps($router->vpn_public_key);
            $router->delete();
            return redirect()->route('routers.index')->with('alert_success', 'Router and VPN peer deleted successfully!');
        } catch (Exception $e) {
            \Log::error('Failed to remove VPN peer during router deletion', [
                'router_id' => $router->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('routers.index')->with('alert_warning', 'Router deleted but VPN peer removal failed. Contact support.');
        }
    }

    public function checkStatus(Router $router) {
        $this->authorize('view', $router);
        $config = new Config([
            'host' => $router->local_ip,
            'user' => $router->api_user,
            'pass' => $router->api_password,
            'port' => 8728,
        ]);

        try {
            $client = new Client($config);
            $responses = $client->query('/system/identity/print')->read();
            $routerName = htmlspecialchars($responses[0]['name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
            
            return back()->with('alert_success', "Connected to: {$routerName}");
        } catch (Exception $e) {
            \Log::error('Router connection failed', ['router_id' => $router->id, 'error' => $e->getMessage()]);
            return back()->with('alert_error', 'Connection failed. Please check your router settings.');
        }
    }

    public function manage(Router $router) {
        $this->authorize('view', $router);
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

            $info = null;
            if (!empty($resourceData)) {
                $info = array_map(function($value) {
                    return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
                }, $resourceData[0]);
            }

            return view('routers.manage', [
                'router' => $router,
                'info' => $info,
                'name' => !empty($identityData) ? htmlspecialchars($identityData[0]['name'], ENT_QUOTES, 'UTF-8') : $router->name
            ]);
        } catch (Exception $e) {
            \Log::error('Router management connection failed', ['router_id' => $router->id, 'error' => $e->getMessage()]);
            return view('routers.manage', [
                'router' => $router,
                'info' => null,
                'name' => $router->name,
                'connectionError' => 'Unable to connect to router. Please check your settings.'
            ]);
        }
    }

    public function clearVouchers(Router $router) {
        $this->authorize('delete', $router);
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
            \Log::error('Voucher clearing failed', ['router_id' => $router->id, 'error' => $e->getMessage()]);
            return redirect()->route('routers.index')
                ->with('alert_error', 'Failed to clear vouchers. Please try again.');
        }
    }

    public function networkTraffic(Router $router) {
        $this->authorize('view', $router);
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
                    $interfaceName = htmlspecialchars($interface['name'], ENT_QUOTES, 'UTF-8');
                    $stats = $client->query([
                        '/interface/monitor-traffic',
                        '=interface=' . $interface['name'],
                        '=duration=1'
                    ])->read();
                    
                    if (!empty($stats)) {
                        $trafficData[] = [
                            'interface' => $interfaceName,
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
