#!/bin/bash
set -e

echo "Starting deployment on Amazon Linux 2023..."

# 1. System Update
echo "[1/7] Updating system..."
sudo dnf update -y

# 2. Install Dependencies
echo "[2/7] Installing Nginx, MariaDB, PHP 8.2, and tools..."
sudo dnf install -y nginx mariadb105-server mariadb105 git unzip curl jq
sudo dnf install -y php php-fpm php-mysqlnd php-cli php-curl php-mbstring php-xml php-bcmath php-zip

# 3. Install Composer
echo "[3/7] Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

# 4. Start & Configure Database
echo "[4/7] Setting up MariaDB..."
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Create DB and user if not exists
sudo mysql -e "CREATE DATABASE IF NOT EXISTS rodud_trucks;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'forge'@'localhost' IDENTIFIED BY 'rodud_secure_pw_123!';" || true
sudo mysql -e "ALTER USER 'forge'@'localhost' IDENTIFIED BY 'rodud_secure_pw_123!';"
sudo mysql -e "GRANT ALL PRIVILEGES ON rodud_trucks.* TO 'forge'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# 5. Setup Project Repository
echo "[5/7] Setting up specific codebase directory..."
cd /home/ec2-user
if [ ! -d "Rodud-Truck-Ordering" ]; then
    echo "Cloning repository..."
    git clone https://github.com/Mehboobiqbal-dev/Rodud-Truck-Ordering.git
fi
cd Rodud-Truck-Ordering/backend

# 6. Configure Laravel Environment
echo "[6/7] Configuring Laravel application..."
sudo -u ec2-user composer install --optimize-autoloader --no-dev

# Setup .env
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Update .env purely using sed (safest non-interactive way)
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=rodud_trucks/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=forge/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=rodud_secure_pw_123!/' .env

# Create required directories for Laravel since git ignores empty directories
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache

# Generate Key and Migrate
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Configure Permissions and Nginx
echo "[7/7] Configuring Permissions and Web Server..."
sudo chown -R nginx:nginx storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Create Nginx Config
sudo cat << 'EOF' > /tmp/rodud.conf
server {
    listen 80;
    server_name _;
    root /home/ec2-user/Rodud-Truck-Ordering/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

sudo mv /tmp/rodud.conf /etc/nginx/conf.d/rodud.conf
# Fix main nginx.conf to avoid conflicting default server
sudo sed -i 's/listen       80 default_server;/listen       80;/g' /etc/nginx/nginx.conf

# Give Nginx user permission to traverse ec2-user home folder
sudo chmod o+x /home/ec2-user
sudo chmod o+x /home/ec2-user/Rodud-Truck-Ordering
sudo chmod o+x /home/ec2-user/Rodud-Truck-Ordering/backend

# Restart Services
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
sudo systemctl restart nginx
sudo systemctl enable nginx

echo "Deployment completed successfully! The API is running at http://13.60.83.143"
