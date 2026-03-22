#!/bin/bash
# ================================================================
#  Docker-Setup — Apache + MySQL + phpMyAdmin
#  Ausführen im jeweiligen Projektordner: bash setup.sh
# ================================================================

set -e

PROJECT_NAME=$(basename "$(pwd)")
DB_NAME="${PROJECT_NAME//-/_}"
DB_USER="appuser"
DB_PASS="apppassword"
DB_ROOT_PASS="rootpassword"

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
DB_PORT=$(find_free_port 3306 "")
PHPMYADMIN_PORT=$(find_free_port 8081 "$WEB_PORT $DB_PORT")

echo ""
echo "==> Projekt: $PROJECT_NAME"
echo "==> Datenbank: $DB_NAME"
echo "==> Ports: Web=$WEB_PORT  MySQL=$DB_PORT  phpMyAdmin=$PHPMYADMIN_PORT"
echo ""

# ----------------------------------------------------------------
#  Verzeichnisstruktur
# ----------------------------------------------------------------
mkdir -p docker/apache
mkdir -p docker/mysql

# ----------------------------------------------------------------
#  .env
# ----------------------------------------------------------------
cat > .env << EOF
MYSQL_DATABASE=${DB_NAME}
MYSQL_USER=${DB_USER}
MYSQL_PASSWORD=${DB_PASS}
MYSQL_ROOT_PASSWORD=${DB_ROOT_PASS}
EOF
echo "✓ .env erstellt"

# ----------------------------------------------------------------
#  docker-compose.yml
# ----------------------------------------------------------------
cat > docker-compose.yml << EOF
version: '3.8'

services:

  web:
    image: php:8.2-apache
    container_name: ${PROJECT_NAME}_web
    ports:
      - "${WEB_PORT}:80"
    volumes:
      - .:/var/www/html
      - ./docker/apache/vhost.conf:/etc/apache2/sites-available/000-default.conf
    depends_on:
      - db
    environment:
      MYSQL_HOST: db
      MYSQL_DATABASE: \${MYSQL_DATABASE}
      MYSQL_USER: \${MYSQL_USER}
      MYSQL_PASSWORD: \${MYSQL_PASSWORD}
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
#  Apache vhost.conf
# ----------------------------------------------------------------
cat > docker/apache/vhost.conf << 'EOF'
<VirtualHost *:80>
    DocumentRoot /var/www/html
    DirectoryIndex index.html index.php

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF
echo "✓ docker/apache/vhost.conf erstellt"

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
#  .gitignore
# ----------------------------------------------------------------
if [ ! -f .gitignore ]; then
  cat > .gitignore << 'EOF'
.env
*.db
node_modules/
EOF
  echo "✓ .gitignore erstellt"
fi

# ----------------------------------------------------------------
#  Docker Compose starten
# ----------------------------------------------------------------
echo ""
echo "==> Starte Docker Container..."
docker compose up -d

echo ""
echo "✓ Setup abgeschlossen!"
echo ""
echo "   Web:        http://localhost:${WEB_PORT}"
echo "   phpMyAdmin: http://localhost:${PHPMYADMIN_PORT}"
echo "   MySQL Port: ${DB_PORT}"
echo ""
