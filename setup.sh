#!/bin/bash

echo "Setting up Passtik Laravel Application..."

# Copy virtual host config
sudo cp /opt/lampp/htdocs/passtik/passtik.conf /opt/lampp/etc/extra/httpd-vhosts-passtik.conf

# Add to hosts file if not exists
if ! grep -q "passtik.local" /etc/hosts; then
    echo "127.0.0.1 passtik.local" | sudo tee -a /etc/hosts
    echo "✓ Added passtik.local to /etc/hosts"
else
    echo "✓ passtik.local already in /etc/hosts"
fi

# Enable virtual hosts in Apache config if not already enabled
if ! grep -q "Include etc/extra/httpd-vhosts-passtik.conf" /opt/lampp/etc/httpd.conf; then
    echo "Include etc/extra/httpd-vhosts-passtik.conf" | sudo tee -a /opt/lampp/etc/httpd.conf
    echo "✓ Enabled virtual host configuration"
else
    echo "✓ Virtual host configuration already enabled"
fi

# Set proper permissions
sudo chown -R $USER:$USER /opt/lampp/htdocs/passtik/storage
sudo chown -R $USER:$USER /opt/lampp/htdocs/passtik/bootstrap/cache
sudo chmod -R 775 /opt/lampp/htdocs/passtik/storage
sudo chmod -R 775 /opt/lampp/htdocs/passtik/bootstrap/cache

echo ""
echo "✓ Setup complete!"
echo ""
echo "Restart Apache with: sudo /opt/lampp/lampp restart"
echo "Then access your app at: http://passtik.local/routers"
echo ""
