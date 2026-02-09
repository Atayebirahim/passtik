<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Exception;

class WireGuardService
{
    private $vpsPrivateKey;
    private $vpsPublicKey;
    private $vpsIp = '10.0.0.1';
    
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
            
            $result = Process::run('wg genkey');
            if (!$result->successful()) {
                throw new Exception('Failed to generate WireGuard key: ' . $result->errorOutput());
            }
            
            $this->vpsPrivateKey = trim($result->output());
            file_put_contents("$keyPath/privatekey", $this->vpsPrivateKey);
            chmod("$keyPath/privatekey", 0600);
            
            $result = Process::run("echo '{$this->vpsPrivateKey}' | wg pubkey");
            if (!$result->successful()) {
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
        $result = Process::run('which wg');
        return $result->successful();
    }
    
    public function generatePeerConfig($routerId)
    {
        if (!$this->isWireGuardInstalled()) {
            throw new Exception('WireGuard is not installed');
        }
        
        $peerIp = "10.0.0." . (10 + $routerId);
        
        $result = Process::run('wg genkey');
        if (!$result->successful()) {
            throw new Exception('Failed to generate peer private key');
        }
        $peerPrivateKey = trim($result->output());
        
        $result = Process::run("echo '{$peerPrivateKey}' | wg pubkey");
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
        $command = "wg set wg0 peer {$peerPublicKey} allowed-ips {$peerIp}/32 persistent-keepalive 25";
        $result = Process::run("sudo $command");
        
        if (!$result->successful()) {
            throw new Exception('Failed to add peer to WireGuard: ' . $result->errorOutput());
        }
        
        $result = Process::run('sudo wg-quick save wg0');
        if (!$result->successful()) {
            throw new Exception('Failed to save WireGuard config: ' . $result->errorOutput());
        }
    }
    
    public function removePeerFromVps($peerPublicKey)
    {
        if (!$peerPublicKey) {
            return; // Old router without VPN
        }
        
        $result = Process::run("sudo wg set wg0 peer {$peerPublicKey} remove");
        if ($result->successful()) {
            Process::run('sudo wg-quick save wg0');
        }
    }
    
    public function generateMikrotikScript($config, $vpsPublicIp, $apiPassword)
    {
        // Escape special characters in password
        $escapedPassword = addslashes($apiPassword);
        
        return <<<SCRIPT
# Passtik WireGuard Auto-Setup Script
# Generated: " . date('Y-m-d H:i:s') . "

/interface wireguard
add listen-port=13231 mtu=1420 name=wireguard-passtik private-key="{$config['peer_private_key']}"

/interface wireguard peers
add allowed-address={$config['vps_ip']}/32 endpoint-address={$vpsPublicIp} endpoint-port=51820 interface=wireguard-passtik public-key="{$config['vps_public_key']}" persistent-keepalive=25s

/ip address
add address={$config['peer_ip']}/24 interface=wireguard-passtik

/ip service
set api address=10.0.0.0/24 port=8728 disabled=no

/user
add name=passtik-api password="{$escapedPassword}" group=full comment="Passtik API"

/ip hotspot walled-garden
add dst-host=www.passtik.net comment="Passtik"
add dst-host=passtik.net comment="Passtik"

:log info "Passtik VPN configured successfully"
:put "VPN IP: {$config['peer_ip']}"
:put "VPS IP: {$config['vps_ip']}"
SCRIPT;
    }
    
    public function getVpsPublicKey()
    {
        return $this->vpsPublicKey;
    }
}}
