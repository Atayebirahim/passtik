<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\DB;
use Exception;

class WireGuardService
{
    private $vpsPrivateKey;
    private $vpsPublicKey;
    private $vpsIp = '10.0.0.1';
    private $maxPeers = 244; // 10.0.0.11 to 10.0.0.254
    
    public function __construct()
    {
        $this->loadOrGenerateVpsKeys();
    }
    
    private function loadOrGenerateVpsKeys()
    {
        $keyPath = storage_path('wireguard');
        
        if (!file_exists($keyPath)) {
            mkdir($keyPath, 0700, true);
        }
        
        if (!file_exists("$keyPath/privatekey")) {
            if (!$this->isWireGuardInstalled()) {
                throw new Exception('WireGuard is not installed. Run: sudo apt install wireguard');
            }
            
            $result = Process::run(['wg', 'genkey']);
            if (!$result->successful()) {
                throw new Exception('Failed to generate WireGuard key');
            }
            
            $this->vpsPrivateKey = trim($result->output());
            file_put_contents("$keyPath/privatekey", $this->vpsPrivateKey);
            chmod("$keyPath/privatekey", 0600);
            
            $result = Process::run(['sh', '-c', 'echo "' . addslashes($this->vpsPrivateKey) . '" | wg pubkey']);
            if (!$result->successful()) {
                \Log::error('Failed to generate VPS public key', ['error' => $result->errorOutput()]);
                throw new Exception('Failed to generate public key');
            }
            
            $this->vpsPublicKey = trim($result->output());
            file_put_contents("$keyPath/publickey", $this->vpsPublicKey);
        } else {
            $this->vpsPrivateKey = trim(file_get_contents("$keyPath/privatekey"));
            $this->vpsPublicKey = trim(file_get_contents("$keyPath/publickey"));
        }
    }
    
    private function isWireGuardInstalled()
    {
        $result = Process::run(['which', 'wg']);
        return $result->successful();
    }
    
    private function getNextAvailableIp()
    {
        // Get all used IPs from database
        $usedIps = DB::table('routers')
            ->whereNotNull('vpn_ip')
            ->pluck('vpn_ip')
            ->map(function($ip) {
                return (int) explode('.', $ip)[3];
            })
            ->toArray();
        
        // Find first available IP starting from 11
        for ($i = 11; $i <= 254; $i++) {
            if (!in_array($i, $usedIps)) {
                return "10.0.0.{$i}";
            }
        }
        
        throw new Exception('No available IP addresses in VPN range (limit: 244 routers)');
    }
    
    public function generatePeerConfig()
    {
        if (!$this->isWireGuardInstalled()) {
            throw new Exception('WireGuard is not installed');
        }
        
        // Check peer limit
        $currentPeers = DB::table('routers')->whereNotNull('vpn_ip')->count();
        if ($currentPeers >= $this->maxPeers) {
            throw new Exception("VPN peer limit reached ({$this->maxPeers} routers maximum)");
        }
        
        $peerIp = $this->getNextAvailableIp();
        
        $result = Process::run(['wg', 'genkey']);
        if (!$result->successful()) {
            throw new Exception('Failed to generate peer private key');
        }
        $peerPrivateKey = trim($result->output());
        
        $result = Process::run(['sh', '-c', 'echo "' . addslashes($peerPrivateKey) . '" | wg pubkey']);
        if (!$result->successful()) {
            throw new Exception('Failed to generate peer public key');
        }
        $peerPublicKey = trim($result->output());
        
        return [
            'peer_ip' => $peerIp,
            'peer_private_key' => $peerPrivateKey,
            'peer_public_key' => $peerPublicKey,
            'vps_public_key' => $this->vpsPublicKey,
            'vps_ip' => $this->vpsIp,
        ];
    }
    
    public function addPeerToVps($peerPublicKey, $peerIp)
    {
        if (!config('wireguard.enabled', true)) {
            \Log::info('WireGuard disabled, skipping peer addition');
            return;
        }
        
        // Validate inputs to prevent command injection
        if (!preg_match('/^[A-Za-z0-9+\/=]{44}$/', $peerPublicKey)) {
            throw new Exception('Invalid peer public key format');
        }
        
        if (!filter_var($peerIp, FILTER_VALIDATE_IP)) {
            throw new Exception('Invalid peer IP address');
        }
        
        $result = Process::run(['sudo', 'wg', 'set', 'wg0', 'peer', $peerPublicKey, 'allowed-ips', $peerIp . '/32', 'persistent-keepalive', '25']);
        
        if (!$result->successful()) {
            \Log::error('Failed to add WireGuard peer', [
                'error' => $result->errorOutput()
            ]);
            throw new Exception('Failed to add peer to WireGuard. Check server logs and sudo permissions.');
        }
        
        $result = Process::run(['sudo', 'wg-quick', 'save', 'wg0']);
        if (!$result->successful()) {
            \Log::error('Failed to save WireGuard config', ['error' => $result->errorOutput()]);
            throw new Exception('Failed to save WireGuard config');
        }
    }
    
    public function removePeerFromVps($peerPublicKey)
    {
        if (!$peerPublicKey) {
            return; // Old router without VPN
        }
        
        if (!config('wireguard.enabled', true)) {
            \Log::info('WireGuard disabled, skipping peer removal');
            return;
        }
        
        // Validate input to prevent command injection
        if (!preg_match('/^[A-Za-z0-9+\/=]{44}$/', $peerPublicKey)) {
            throw new Exception('Invalid peer public key format');
        }
        
        $result = Process::run(['sudo', 'wg', 'set', 'wg0', 'peer', $peerPublicKey, 'remove']);
        if (!$result->successful()) {
            \Log::error('Failed to remove WireGuard peer', [
                'error' => $result->errorOutput()
            ]);
            throw new Exception('Failed to remove peer from VPN');
        }
        
        $result = Process::run(['sudo', 'wg-quick', 'save', 'wg0']);
        if (!$result->successful()) {
            \Log::warning('Failed to save WireGuard config after peer removal');
        }
    }
    
    public function checkPeerStatus($peerPublicKey)
    {
        if (!$peerPublicKey) {
            return ['connected' => false, 'message' => 'No VPN configured'];
        }
        
        // Validate input
        if (!preg_match('/^[A-Za-z0-9+\/=]{44}$/', $peerPublicKey)) {
            return ['connected' => false, 'message' => 'Invalid key format'];
        }
        
        $result = Process::run(['sudo', 'wg', 'show', 'wg0', 'peers']);
        if (!$result->successful()) {
            return ['connected' => false, 'message' => 'Cannot check VPN status'];
        }
        
        $peers = explode("\n", trim($result->output()));
        $connected = in_array($peerPublicKey, $peers);
        
        if ($connected) {
            // Get peer details
            $result = Process::run(['sudo', 'wg', 'show', 'wg0', 'dump']);
            if ($result->successful()) {
                $lines = explode("\n", trim($result->output()));
                foreach ($lines as $line) {
                    if (strpos($line, $peerPublicKey) !== false) {
                        $parts = explode("\t", $line);
                        $lastHandshake = isset($parts[4]) ? (int)$parts[4] : 0;
                        $isActive = $lastHandshake > 0 && (time() - $lastHandshake) < 180; // 3 minutes
                        
                        return [
                            'connected' => true,
                            'active' => $isActive,
                            'last_handshake' => $lastHandshake,
                            'message' => $isActive ? 'VPN Active' : 'VPN Configured (Not Active)'
                        ];
                    }
                }
            }
        }
        
        return ['connected' => false, 'message' => 'VPN Not Connected'];
    }
    
    public function generateMikrotikScript($config, $vpsPublicIp, $apiPassword)
    {
        // Validate inputs
        if (!filter_var($vpsPublicIp, FILTER_VALIDATE_IP)) {
            throw new Exception('Invalid VPS IP address');
        }
        
        if (!filter_var($config['peer_ip'], FILTER_VALIDATE_IP)) {
            throw new Exception('Invalid peer IP address');
        }
        
        if (!filter_var($config['vps_ip'], FILTER_VALIDATE_IP)) {
            throw new Exception('Invalid VPS IP address');
        }
        
        // Validate WireGuard keys format
        if (!preg_match('/^[A-Za-z0-9+\/=]{44}$/', $config['peer_private_key'])) {
            throw new Exception('Invalid peer private key format');
        }
        
        if (!preg_match('/^[A-Za-z0-9+\/=]{44}$/', $config['vps_public_key'])) {
            throw new Exception('Invalid VPS public key format');
        }
        
        // Escape special characters in password for MikroTik script
        $escapedPassword = str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $apiPassword);
        $timestamp = date('Y-m-d H:i:s');
        
        return <<<SCRIPT
# Passtik WireGuard Auto-Setup Script
# Generated: {$timestamp}

/interface wireguard
add listen-port=13231 mtu=1420 name=wireguard-passtik private-key="{$config['peer_private_key']}"

/interface wireguard peers
add allowed-address={$config['vps_ip']}/32 endpoint-address={$vpsPublicIp} endpoint-port=51820 interface=wireguard-passtik public-key="{$config['vps_public_key']}" persistent-keepalive=25s

/ip address
add address={$config['peer_ip']}/24 interface=wireguard-passtik

/ip service
set api address=10.0.0.0/24,192.168.0.0/16 port=8728 disabled=no

/user
add name=passtik-api password="{$escapedPassword}" group=full comment="Passtik API"

/ip hotspot walled-garden
add dst-host=www.passtik.net comment="Passtik"
add dst-host=passtik.net comment="Passtik"

:log info "Passtik VPN configured successfully"
:put "VPN IP: {$config['peer_ip']}"
:put "VPS IP: {$config['vps_ip']}"
:put "VPS Endpoint: {$vpsPublicIp}:51820"
SCRIPT;
    }
    
    public function getVpsPublicKey()
    {
        return $this->vpsPublicKey;
    }
}
