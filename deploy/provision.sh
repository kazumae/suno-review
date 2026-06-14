#!/usr/bin/env bash
# サーバー初期構築（Ubuntu 24.04 / Lightsail micro）。root で実行。
#   sudo bash provision.sh <db_password>
set -euo pipefail

DB_PASSWORD="${1:?usage: provision.sh <db_password>}"
DOMAIN="suno.kazumae.net"
APP_DIR="/var/www/suno/laravel"
PHP_SOCK="/var/run/php/php8.4-fpm.sock"

export DEBIAN_FRONTEND=noninteractive

echo "==> swap 2GB"
if [ ! -f /swapfile ]; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

echo "==> base packages"
apt-get update -y
apt-get install -y software-properties-common curl unzip git rsync ca-certificates
add-apt-repository -y ppa:ondrej/php
apt-get update -y

echo "==> PHP 8.4"
apt-get install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-mbstring \
    php8.4-xml php8.4-curl php8.4-zip php8.4-gd php8.4-intl php8.4-bcmath

echo "==> nginx / mysql"
apt-get install -y nginx mysql-server

echo "==> Node 20"
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs

echo "==> Composer"
if [ ! -f /usr/local/bin/composer ]; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "==> certbot"
apt-get install -y certbot python3-certbot-nginx

echo "==> MySQL database + user"
mysql <<SQL
CREATE DATABASE IF NOT EXISTS suno CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'suno'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER 'suno'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON suno.* TO 'suno'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "==> MySQL low-memory tuning"
cat > /etc/mysql/mysql.conf.d/low-memory.cnf <<'CNF'
[mysqld]
innodb_buffer_pool_size = 128M
performance_schema = OFF
max_connections = 40
CNF
systemctl restart mysql

echo "==> PHP-FPM pool tuning (low memory)"
sed -i 's/^pm.max_children = .*/pm.max_children = 8/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/^pm.start_servers = .*/pm.start_servers = 2/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/^pm.min_spare_servers = .*/pm.min_spare_servers = 1/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/^pm.max_spare_servers = .*/pm.max_spare_servers = 3/' /etc/php/8.4/fpm/pool.d/www.conf
sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 20M/' /etc/php/8.4/fpm/php.ini || true
sed -i 's/^post_max_size = .*/post_max_size = 25M/' /etc/php/8.4/fpm/php.ini || true
systemctl restart php8.4-fpm

echo "==> app dir"
mkdir -p "${APP_DIR}"
chown -R ubuntu:www-data /var/www/suno

echo "==> nginx site"
cat > /etc/nginx/sites-available/suno <<NGINX
server {
    listen 80;
    server_name ${DOMAIN};
    root ${APP_DIR}/public;
    index index.php;
    client_max_body_size 20M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_pass unix:${PHP_SOCK};
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX
ln -sf /etc/nginx/sites-available/suno /etc/nginx/sites-enabled/suno
rm -f /etc/nginx/sites-enabled/default

echo "==> systemd: inertia ssr"
cat > /etc/systemd/system/inertia-ssr.service <<UNIT
[Unit]
Description=Inertia SSR server (suno)
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=${APP_DIR}
Environment=PATH=/usr/local/bin:/usr/bin:/bin
ExecStart=/usr/bin/php ${APP_DIR}/artisan inertia:start-ssr
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
UNIT

echo "==> systemd: queue worker"
cat > /etc/systemd/system/suno-queue.service <<UNIT
[Unit]
Description=Laravel queue worker (suno)
After=network.target mysql.service

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php ${APP_DIR}/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
UNIT

systemctl daemon-reload
systemctl enable nginx php8.4-fpm mysql >/dev/null 2>&1 || true
# inertia-ssr / suno-queue はコードデプロイ後に起動する
systemctl restart nginx

echo "PROVISION_DONE"
