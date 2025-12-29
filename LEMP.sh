#!/bin/bash

# =============================================================================
# LEMP Stack Setup Script for Yii2 Blog
# Site: wizai.ru
# =============================================================================
set -e

# === КОНФИГУРАЦИЯ ===
DOMAIN="wizai.ru"
SITE_ROOT="/var/www/$DOMAIN"

DB_NAME="wizai"
DB_USER="wizai"
DB_PASS="WizAi2025Secure!"

ADMIN_EMAIL="admin@wizai.ru"

REPO_URL="https://github.com/AlmiroN-code/yii2.git"

export COMPOSER_ALLOW_SUPERUSER=1

# Цвета
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[OK]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

echo ""
echo "=============================================="
echo "  LEMP Stack для Yii2 Blog"
echo "  Site: $DOMAIN"
echo "=============================================="
echo ""

# === 1. Проверка root ===
if [[ $EUID -ne 0 ]]; then
   log_error "Скрипт должен запускаться от root"
   exit 1
fi

# === 2. Обновление системы ===
log_info "Обновляю систему..."
apt update && apt upgrade -y
apt install -y curl wget gnupg2 software-properties-common ca-certificates \
    lsb-release apt-transport-https git unzip htop fail2ban ufw
log_success "Система обновлена"

# === 3. Nginx ===
if ! command -v nginx &> /dev/null; then
    log_info "Устанавливаю Nginx..."
    apt install -y nginx
    systemctl enable --now nginx
    log_success "Nginx установлен"
else
    log_warn "Nginx уже установлен"
fi

# === 4. MariaDB ===
if ! command -v mariadb &> /dev/null; then
    log_info "Устанавливаю MariaDB..."
    apt install -y mariadb-server mariadb-client
    systemctl enable --now mariadb
    log_success "MariaDB установлен"
else
    log_warn "MariaDB уже установлен"
fi

# === 5. PHP 8.4 + расширения для Yii2 ===
if ! command -v php8.4 &> /dev/null; then
    log_info "Добавляю репозиторий PHP 8.4..."
    add-apt-repository -y ppa:ondrej/php
    apt update

    log_info "Устанавливаю PHP 8.4 и расширения для Yii2..."
    apt install -y \
        php8.4-fpm \
        php8.4-cli \
        php8.4-common \
        php8.4-mysql \
        php8.4-curl \
        php8.4-gd \
        php8.4-mbstring \
        php8.4-xml \
        php8.4-zip \
        php8.4-bcmath \
        php8.4-intl \
        php8.4-opcache \
        php8.4-imagick
    
    systemctl enable --now php8.4-fpm
    log_success "PHP 8.4 установлен"
else
    log_warn "PHP 8.4 уже установлен"
fi

# === 6. Composer ===
if ! command -v composer &> /dev/null; then
    log_info "Устанавливаю Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    log_success "Composer установлен"
else
    log_warn "Composer уже установлен"
fi

# === 7. Node.js 20 LTS ===
if ! command -v node &> /dev/null; then
    log_info "Устанавливаю Node.js 20 LTS..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt install -y nodejs
    log_success "Node.js установлен"
else
    log_warn "Node.js уже установлен"
fi

# === 8. Переключение PHP CLI на 8.4 ===
log_info "Переключаю PHP CLI на 8.4..."
update-alternatives --set php /usr/bin/php8.4 2>/dev/null || true
log_success "PHP CLI = 8.4"

# === 9. Настройка БД ===
if ! mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME" 2>/dev/null; then
    log_info "Создаю БД $DB_NAME..."
    sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
    sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
    sudo mysql -e "FLUSH PRIVILEGES;"
    log_success "БД создана"
else
    log_warn "БД уже существует"
fi

# === 10. Клонирование Yii2 проекта ===
log_info "Клонирую Yii2 проект..."
if [ -d "$SITE_ROOT" ]; then
    rm -rf "$SITE_ROOT"
fi

git clone "$REPO_URL" "$SITE_ROOT"
cd "$SITE_ROOT"
log_success "Репозиторий склонирован"

# === 11. Конфигурация БД для Yii2 ===
log_info "Создаю config/db.php..."
cat > "$SITE_ROOT/config/db.php" << DBEOF
<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=$DB_NAME',
    'username' => '$DB_USER',
    'password' => '$DB_PASS',
    'charset' => 'utf8mb4',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
DBEOF
log_success "config/db.php создан"

# === 12. Генерируем cookieValidationKey ===
log_info "Генерирую cookieValidationKey..."
COOKIE_KEY=$(openssl rand -base64 32)
sed -i "s/'cookieValidationKey' => '[^']*'/'cookieValidationKey' => '$COOKIE_KEY'/" "$SITE_ROOT/config/web.php" 2>/dev/null || true
log_success "cookieValidationKey установлен"

# === 13. Composer зависимости ===
log_info "Устанавливаю Composer зависимости..."
cd "$SITE_ROOT"
composer install --no-dev --optimize-autoloader --no-interaction
log_success "Composer установлен"

# === 13.1. Production режим (ПОСЛЕ composer install) ===
log_info "Настраиваю Yii2 для production..."

# Создаём production версию web/index.php
cat > "$SITE_ROOT/web/index.php" << 'INDEXEOF'
<?php

// Production mode
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
INDEXEOF

# Создаём production версию yii (console)
cat > "$SITE_ROOT/yii" << 'YIIEOF'
#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 */

// Production mode
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/console.php';

$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);
YIIEOF

chmod +x "$SITE_ROOT/yii"

log_success "Production режим установлен"

# === 14. NPM зависимости и сборка ===
log_info "Устанавливаю npm зависимости..."
npm ci --production=false
log_success "npm установлен"

log_info "Собираю фронтенд (TailwindCSS)..."
npm run build
log_success "Фронтенд собран"

# === 15. Миграции Yii2 ===
log_info "Выполняю миграции..."
php yii migrate --interactive=0
log_success "Миграции выполнены"

# === 16. Создание директорий для загрузок ===
log_info "Создаю директории для загрузок..."
mkdir -p "$SITE_ROOT/web/uploads/avatars"
mkdir -p "$SITE_ROOT/web/uploads/publications"
mkdir -p "$SITE_ROOT/web/uploads/settings"
mkdir -p "$SITE_ROOT/runtime"
mkdir -p "$SITE_ROOT/web/assets"
log_success "Директории созданы"

# === 17. Права доступа ===
log_info "Настраиваю права..."
chown -R www-data:www-data "$SITE_ROOT"
chmod -R 775 "$SITE_ROOT/runtime"
chmod -R 775 "$SITE_ROOT/web/assets"
chmod -R 775 "$SITE_ROOT/web/uploads"
log_success "Права настроены"

# === 18. Nginx конфигурация для Yii2 ===
log_info "Настраиваю Nginx..."
rm -f /etc/nginx/sites-enabled/default

cat > /etc/nginx/sites-available/$DOMAIN << 'NGINXEOF'
server {
    listen 80;
    listen [::]:80;
    server_name wizai.ru www.wizai.ru;

    root /var/www/wizai.ru/web;
    index index.php;

    access_log /var/log/nginx/wizai.ru_access.log;
    error_log /var/log/nginx/wizai.ru_error.log;

    client_max_body_size 50M;

    charset utf-8;

    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript image/svg+xml;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_read_timeout 300;
        try_files $uri =404;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location ~ /\.(ht|git|svn) {
        deny all;
    }

    location = /favicon.ico {
        log_not_found off;
        access_log off;
    }

    location = /robots.txt {
        log_not_found off;
        access_log off;
    }
}
NGINXEOF

ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/

nginx -t
log_success "Nginx настроен"

# === 19. PHP-FPM конфигурация ===
log_info "Настраиваю PHP-FPM..."
cat > /etc/php/8.4/fpm/conf.d/99-yii2.ini << 'PHPINI'
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
expose_php = Off
display_errors = Off
log_errors = On
session.cookie_httponly = 1
PHPINI
log_success "PHP-FPM настроен"

# === 20. Firewall ===
log_info "Настраиваю Firewall..."
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 'Nginx Full'
ufw --force enable
log_success "Firewall настроен"

# === 21. Fail2Ban ===
log_info "Настраиваю Fail2Ban..."
cat > /etc/fail2ban/jail.local << 'F2BEOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
maxretry = 3

[nginx-http-auth]
enabled = true
F2BEOF
systemctl enable --now fail2ban
log_success "Fail2Ban настроен"

# === 22. Certbot ===
if ! command -v certbot &> /dev/null; then
    log_info "Устанавливаю Certbot..."
    apt install -y certbot python3-certbot-nginx
    log_success "Certbot установлен"
fi

# === 23. Перезапуск сервисов ===
log_info "Перезапускаю сервисы..."
systemctl restart php8.4-fpm
systemctl restart nginx
log_success "Сервисы перезапущены"

# === 24. Сохранение данных ===
cat > /root/.wizai_credentials << CREDEOF
============================================
  WizAI Server Credentials
  Created: $(date)
============================================

DATABASE:
  DB: $DB_NAME
  User: $DB_USER
  Password: $DB_PASS

URLS:
  Site: http://$DOMAIN

PATHS:
  Root: $SITE_ROOT
  Web Root: $SITE_ROOT/web
  Logs: /var/log/nginx/

YIICONSOLE:
  php yii migrate
  php yii cache/flush-all

SSL:
  sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN

============================================
CREDEOF
chmod 600 /root/.wizai_credentials

# === ФИНАЛ ===
echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  ✅ УСТАНОВКА ЗАВЕРШЕНА!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "🌐 ${BLUE}Сайт:${NC} http://$DOMAIN"
echo ""
echo -e "${YELLOW}=== БД ===${NC}"
echo -e "DB:   $DB_NAME"
echo -e "User: $DB_USER"
echo -e "Pass: $DB_PASS"
echo ""
echo -e "📄 Данные: ${BLUE}/root/.wizai_credentials${NC}"
echo ""
echo -e "${YELLOW}=== SSL ===${NC}"
echo "sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN"
echo ""
echo -e "${YELLOW}=== Yii2 команды ===${NC}"
echo "cd $SITE_ROOT"
echo "php yii migrate"
echo "php yii cache/flush-all"
echo ""
