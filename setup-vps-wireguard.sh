#!/bin/bash
# Passtik VPS WireGuard Setup Script
# Run this ONCE on your VPS to initialize WireGuard

set -e

echo "=== Passtik VPS WireGuard Setup ==="

# Install WireGuard
echo "Installing WireGuard..."
apt update
apt install -y wireguard

# Enable IP forwarding
echo "Enabling IP forwarding..."
echo "net.ipv4.ip_forward=1" >> /etc/sysctl.conf
sysctl -p

# Generate VPS keys (Laravel will do this, but we need interface)
cd /etc/wireguard

# Create base config
cat > wg0.conf << 'EOF'
[Interface]
Address = 10.0.0.1/24
ListenPort = 51820
SaveConfig = true
PostUp = iptables -A FORWARD -i wg0 -j ACCEPT; iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
PostDown = iptables -D FORWARD -i wg0 -j ACCEPT; iptables -t nat -D POSTROUTING -o eth0 -j MASQUERADE
EOF

# Generate keys
wg genkey | tee privatekey | wg pubkey > publickey
PRIVATE_KEY=$(cat privatekey)

# Update config with private key
sed -i "/\[Interface\]/a PrivateKey = $PRIVATE_KEY" wg0.conf

# Set permissions
chmod 600 wg0.conf privatekey
chmod 644 publickey

# Enable and start WireGuard
systemctl enable wg-quick@wg0
systemctl start wg-quick@wg0

# Open firewall
ufw allow 51820/udp

# Create Laravel storage directory
mkdir -p /var/www/passtik/storage/wireguard
cp privatekey publickey /var/www/passtik/storage/wireguard/
chown -R www-data:www-data /var/www/passtik/storage/wireguard
chmod 700 /var/www/passtik/storage/wireguard
chmod 600 /var/www/passtik/storage/wireguard/privatekey

echo ""
echo "=== Setup Complete ==="
echo "VPS Public Key: $(cat publickey)"
echo "VPN IP: 10.0.0.1"
echo ""
echo "WireGuard is running. Laravel will automatically add peers when routers are added."
echo ""
echo "Test with: wg show"
