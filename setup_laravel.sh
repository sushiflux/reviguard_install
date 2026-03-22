#!/bin/bash
# ================================================================
#  Docker-Setup — Laravel + Nginx + MySQL + phpMyAdmin
#  Ausführen im jeweiligen Projektordner: bash setup_laravel.sh
# ================================================================

set -e

PROJECT_NAME=$(basename "$(pwd)")
DB_NAME="${PROJECT_NAME//-/_}"
DB_USER="appuser"
DB_PASS="apppassword"
DB_ROOT_PASS="rootpassword"
APP_KEY="base64:$(openssl rand -base64 32)"

# ----------------------------------------------------------------
#  Aktuellen User ermitteln (für Datei-Ownership im Container)
# ----------------------------------------------------------------
HOST_UID=$(id -u)
HOST_GID=$(id -g)
HOST_USER=$(id -un)

# ----------------------------------------------------------------
#  Freien Port finden
# ----------------------------------------------------------------
port_in_use() {
    ss -tlnH | awk '{print $4}' | grep -q ":${1}$"
}

find_free_port() {
    local port=$1
    local reserved=$2
    while port_in_use $port || echo "$reserved" | grep -qw "$port"; do
        port=$((port + 1))
    done
    echo $port
}

WEB_PORT=$(find_free_port 8080 "")
DB_PORT=$(find_free_port 3306 "$WEB_PORT")
PHPMYADMIN_PORT=$(find_free_port 8081 "$WEB_PORT $DB_PORT")

echo ""
echo "==> Projekt:    $PROJECT_NAME"
echo "==> Datenbank:  $DB_NAME"
echo "==> Ports:      Web=$WEB_PORT  MySQL=$DB_PORT  phpMyAdmin=$PHPMYADMIN_PORT"
echo "==> User:       $HOST_USER (UID=$HOST_UID / GID=$HOST_GID)"
echo ""

# ----------------------------------------------------------------
#  Verzeichnisstruktur
# ----------------------------------------------------------------
mkdir -p docker/nginx
mkdir -p docker/php
mkdir -p docker/mysql

# ----------------------------------------------------------------
#  .env (Laravel-Format)
# ----------------------------------------------------------------
cat > .env << EOF
APP_NAME=${PROJECT_NAME}
APP_ENV=local
APP_KEY=${APP_KEY}
APP_DEBUG=true
APP_URL=http://localhost:${WEB_PORT}

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}

MYSQL_DATABASE=${DB_NAME}
MYSQL_USER=${DB_USER}
MYSQL_PASSWORD=${DB_PASS}
MYSQL_ROOT_PASSWORD=${DB_ROOT_PASS}
EOF
echo "✓ .env erstellt"

# ----------------------------------------------------------------
#  Dockerfile — PHP-FPM läuft als Host-User (keine Root-Konflikte)
# ----------------------------------------------------------------
cat > docker/php/Dockerfile << 'EOF'
FROM php:8.2-fpm

# System-Abhängigkeiten
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Host-User im Container anlegen (UID/GID kommen als Build-Args)
ARG HOST_UID=1000
ARG HOST_GID=1000
RUN groupadd -g ${HOST_GID} appgroup 2>/dev/null || true \
    && useradd -u ${HOST_UID} -g ${HOST_GID} -m -s /bin/bash appuser 2>/dev/null || true

# Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

# PHP-FPM als Host-User starten
USER appuser

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
EOF
echo "✓ docker/php/Dockerfile erstellt"

# ----------------------------------------------------------------
#  Entrypoint — kein chown nötig, php-fpm läuft als Host-User
# ----------------------------------------------------------------
cat > docker/php/entrypoint.sh << 'EOF'
#!/bin/sh
exec php-fpm
EOF
chmod +x docker/php/entrypoint.sh
echo "✓ docker/php/entrypoint.sh erstellt"

# ----------------------------------------------------------------
#  Nginx-Konfiguration (Document Root → public/)
# ----------------------------------------------------------------
cat > docker/nginx/default.conf << 'EOF'
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass   php:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
}
EOF
echo "✓ docker/nginx/default.conf erstellt"

# ----------------------------------------------------------------
#  MySQL init.sql
# ----------------------------------------------------------------
cat > docker/mysql/init.sql << EOF
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE \`${DB_NAME}\`;
EOF
echo "✓ docker/mysql/init.sql erstellt"

# ----------------------------------------------------------------
#  docker-compose.yml — Build-Args mit Host-UID/GID übergeben
# ----------------------------------------------------------------
cat > docker-compose.yml << EOF
version: '3.8'

services:

  php:
    build:
      context: ./docker/php
      dockerfile: Dockerfile
      args:
        HOST_UID: ${HOST_UID}
        HOST_GID: ${HOST_GID}
    container_name: ${PROJECT_NAME}_php
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    networks:
      - appnet

  web:
    image: nginx:1.25-alpine
    container_name: ${PROJECT_NAME}_web
    ports:
      - "${WEB_PORT}:80"
    volumes:
      - .:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - php
    networks:
      - appnet

  db:
    image: mysql:8.0
    container_name: ${PROJECT_NAME}_db
    restart: always
    ports:
      - "${DB_PORT}:3306"
    environment:
      MYSQL_DATABASE: \${MYSQL_DATABASE}
      MYSQL_USER: \${MYSQL_USER}
      MYSQL_PASSWORD: \${MYSQL_PASSWORD}
      MYSQL_ROOT_PASSWORD: \${MYSQL_ROOT_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
      - ./docker/mysql/init.sql:/docker-entrypoint-initdb.d/init.sql
    networks:
      - appnet

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: ${PROJECT_NAME}_phpmyadmin
    ports:
      - "${PHPMYADMIN_PORT}:80"
    environment:
      PMA_HOST: db
      MYSQL_ROOT_PASSWORD: \${MYSQL_ROOT_PASSWORD}
    depends_on:
      - db
    networks:
      - appnet

volumes:
  db_data:

networks:
  appnet:
    driver: bridge
EOF
echo "✓ docker-compose.yml erstellt"

# ----------------------------------------------------------------
#  .gitignore
# ----------------------------------------------------------------
if [ ! -f .gitignore ]; then
  cat > .gitignore << 'EOF'
.env
*.db
node_modules/
vendor/
storage/*.key
storage/app/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
storage/logs/*
!storage/**/.gitkeep
bootstrap/cache/*
!bootstrap/cache/.gitkeep
public/hot
public/storage
EOF
  echo "✓ .gitignore erstellt"
fi

# ----------------------------------------------------------------
#  Docker Container bauen und starten
# ----------------------------------------------------------------
echo ""
echo "==> Baue Docker Images..."
docker compose build

echo ""
echo "==> Starte Docker Container..."
docker compose up -d

# ----------------------------------------------------------------
#  Warten bis MySQL bereit ist
# ----------------------------------------------------------------
echo ""
echo "==> Warte auf MySQL..."
until docker compose exec db mysqladmin ping -h "localhost" -u"${DB_USER}" -p"${DB_PASS}" --silent 2>/dev/null; do
    echo "   MySQL noch nicht bereit — warte 2 Sekunden..."
    sleep 2
done
echo "✓ MySQL bereit"

# ----------------------------------------------------------------
#  Laravel installieren (falls noch nicht vorhanden)
# ----------------------------------------------------------------
if [ ! -f "artisan" ]; then
    echo ""
    echo "==> Installiere Laravel via Composer (temporärer Pfad)..."
    docker compose exec php composer create-project laravel/laravel /tmp/laravel_install --prefer-dist

    echo ""
    echo "==> Verschiebe Laravel-Dateien in den Projektordner..."
    docker compose exec php bash -c "
        shopt -s dotglob
        cp -rn /tmp/laravel_install/* /var/www/html/
        rm -rf /tmp/laravel_install
    "

    echo ""
    echo "==> Setze APP_KEY..."
    docker compose exec php php artisan key:generate --force

    echo ""
    echo "==> Führe Migrationen aus..."
    docker compose exec php php artisan migrate --force
else
    echo ""
    echo "==> Laravel bereits vorhanden — überspringe Installation."
    echo "==> Führe Migrationen aus..."
    docker compose exec php php artisan migrate --force
fi

# ----------------------------------------------------------------
#  Ownership auf Host-User setzen
# ----------------------------------------------------------------
echo ""
echo "==> Setze Datei-Ownership auf $HOST_USER (UID=$HOST_UID)..."
chown -R "${HOST_UID}:${HOST_GID}" . 2>/dev/null || \
    docker compose exec -u root php chown -R "${HOST_UID}:${HOST_GID}" /var/www/html
echo "✓ Ownership gesetzt"

echo ""
echo "✓ Laravel-Setup abgeschlossen!"
echo ""
echo "   App:        http://localhost:${WEB_PORT}"
echo "   phpMyAdmin: http://localhost:${PHPMYADMIN_PORT}"
echo "   MySQL Port: ${DB_PORT}"
echo ""
