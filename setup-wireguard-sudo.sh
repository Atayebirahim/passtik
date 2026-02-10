#!/bin/bash
# Configure sudo permissions for WireGuard commands
# Run this on your VPS as root

echo "Configuring sudo permissions for www-data user..."

# Create sudoers file for WireGuard
cat > /etc/sudoers.d/passtik-wireguard << 'EOF'
# Allow www-data to manage WireGuard without password
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg
www-data ALL=(ALL) NOPASSWD: /usr/bin/wg-quick
EOF

# Set correct permissions
chmod 0440 /etc/sudoers.d/passtik-wireguard

# Verify syntax
visudo -c -f /etc/sudoers.d/passtik-wireguard

if [ $? -eq 0 ]; then
    echo "✓ Sudoers configuration created successfully"
    echo "✓ www-data can now run WireGuard commands"
else
    echo "✗ Error in sudoers configuration"
    rm /etc/sudoers.d/passtik-wireguard
    exit 1
fi

# Test permissions
echo ""
echo "Testing permissions..."
sudo -u www-data wg show > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✓ Permission test passed"
else
    echo "✗ Permission test failed"
fi

echo ""
echo "Setup complete!"
