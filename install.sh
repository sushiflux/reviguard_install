#!/usr/bin/env bash
# ================================================================
#  ReviGuard — Professioneller Installations-Assistent
#  Version: 1.0
#
#  Unterstützte Systeme:
#    • Ubuntu 20.04 / 22.04 / 24.04
#    • Debian 11 / 12
#    • Rocky Linux 8 / 9
#    • AlmaLinux 8 / 9
#    • RHEL 8 / 9
#    • Fedora 38+
# ================================================================
set -euo pipefail
IFS=$'\n\t'

REVIGUARD_VERSION="0.5.2"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LOG_FILE="/tmp/reviguard-install.log"
STEP_COUNT=1

# ── Mindestanforderungen ──────────────────────────────────────────
MIN_DISK_MB=2048
MIN_RAM_MB=512

# ── Farben ────────────────────────────────────────────────────────
RED='\033[0;31m'; GRN='\033[0;32m'; YLW='\033[1;33m'
CYN='\033[0;36m'; BLU='\033[0;34m'; MAG='\033[0;35m'
BLD='\033[1m';    DIM='\033[2m';    RST='\033[0m'

# ── Ausgabe-Hilfsfunktionen ────────────────────────────────────────
banner() {
  clear
  echo -e "${CYN}${BLD}"
  echo "  ██████╗ ███████╗██╗   ██╗██╗ ██████╗ ██╗   ██╗ █████╗ ██████╗ ██████╗ "
  echo "  ██╔══██╗██╔════╝██║   ██║██║██╔════╝ ██║   ██║██╔══██╗██╔══██╗██╔══██╗"
  echo "  ██████╔╝█████╗  ██║   ██║██║██║  ███╗██║   ██║███████║██████╔╝██║  ██║"
  echo "  ██╔══██╗██╔══╝  ╚██╗ ██╔╝██║██║   ██║██║   ██║██╔══██║██╔══██╗██║  ██║"
  echo "  ██║  ██║███████╗ ╚████╔╝ ██║╚██████╔╝╚██████╔╝██║  ██║██║  ██║██████╔╝"
  echo "  ╚═╝  ╚═╝╚══════╝  ╚═══╝  ╚═╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝╚═════╝ "
  echo -e "${RST}"
  echo -e "  ${DIM}Versions-Management System  ·  v${REVIGUARD_VERSION}  ·  Installations-Assistent${RST}"
  echo -e "  ${DIM}──────────────────────────────────────────────────────────────${RST}"
  echo
}

step() {
  echo
  echo -e "${BLD}${BLU}  ╔══════════════════════════════════════════════════════════╗${RST}"
  printf "${BLD}${BLU}  ║  Schritt %-2s — %-44s║\n${RST}" "$STEP_COUNT" "$*"
  echo -e "${BLD}${BLU}  ╚══════════════════════════════════════════════════════════╝${RST}"
  ((STEP_COUNT++)) || true
}

ok()    { echo -e "  ${GRN}✔${RST}  $*"; }
warn()  { echo -e "  ${YLW}⚠${RST}  $*"; }
info()  { echo -e "  ${CYN}ℹ${RST}  $*"; }
err()   { echo -e "  ${RED}✖${RST}  $*" >&2; }
sep()   { echo -e "  ${DIM}──────────────────────────────────────────────────────${RST}"; }
log()   { echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" >> "$LOG_FILE"; }

die() {
  echo
  err "$*"
  echo
  echo -e "  ${RED}${BLD}Installation abgebrochen.${RST}"
  echo -e "  ${DIM}Protokoll für Details: $LOG_FILE${RST}"
  echo
  exit 1
}

ask() {
  local prompt="$1" default="${2:-}" result
  echo -n -e "  ${BLD}→${RST} " >&2
  if [[ -n "$default" ]]; then
    echo -n -e "$prompt ${DIM}[$default]${RST}: " >&2
  else
    echo -n -e "$prompt: " >&2
  fi
  read -r result
  echo "${result:-${default}}"
}

ask_secret() {
  local prompt="$1" result
  echo -n -e "  ${BLD}→${RST} $prompt: " >&2
  read -rs result; echo >&2
  echo "$result"
}

ask_yn() {
  local prompt="$1" default="${2:-j}" result label
  [[ "$default" == "j" ]] && label="${BLD}J${RST}/n" || label="j/${BLD}N${RST}"
  echo -n -e "  ${BLD}→${RST} $prompt [$label]: " >&2
  read -r result
  result="${result:-$default}"
  [[ "$result" =~ ^[JjYy1]$ ]]
}

# ── Spinner für lange Operationen ────────────────────────────────
SPINNER_PID=""
spinner_start() {
  local msg="$1"
  local frames=('⠋' '⠙' '⠹' '⠸' '⠼' '⠴' '⠦' '⠧' '⠇' '⠏')
  (
    local i=0
    while true; do
      printf "\r  ${CYN}%s${RST}  %s " "${frames[$i]}" "$msg" >&2
      ((i = (i + 1) % ${#frames[@]})) || true
      sleep 0.1
    done
  ) &
  SPINNER_PID=$!
}

spinner_stop() {
  if [[ -n "$SPINNER_PID" ]]; then
    kill "$SPINNER_PID" 2>/dev/null || true
    wait "$SPINNER_PID" 2>/dev/null || true
    SPINNER_PID=""
    printf "\r\033[K" >&2
  fi
}

# Sicherstellen, dass Spinner bei Abbruch stoppt
trap 'spinner_stop' EXIT INT TERM

# ── Passwort generieren ────────────────────────────────────────────
gen_password() {
  tr -dc 'A-Za-z0-9@#%^&*' < /dev/urandom 2>/dev/null | head -c 24 || true
}

# ── Betriebssystem erkennen ────────────────────────────────────────
OS_ID="unknown"; OS_VERSION="0"; OS_FAMILY="unknown"; OS_NAME="Unbekannt"
detect_os() {
  if [[ -f /etc/os-release ]]; then
    # shellcheck disable=SC1091
    . /etc/os-release
    OS_ID="${ID:-unknown}"
    OS_VERSION="${VERSION_ID:-0}"
    OS_NAME="${PRETTY_NAME:-$OS_ID}"
    case "$OS_ID" in
      ubuntu|debian|linuxmint|pop)         OS_FAMILY="debian" ;;
      rhel|centos|rocky|almalinux|fedora)  OS_FAMILY="rhel"   ;;
    esac
  fi
  if [[ "$OS_FAMILY" == "unknown" ]]; then
    die "Nicht unterstütztes Betriebssystem. Unterstützt: Ubuntu, Debian, Rocky Linux, AlmaLinux, RHEL, Fedora."
  fi
}

# ── Docker / Compose ──────────────────────────────────────────────
DOCKER_SUDO=""
DOCKER_COMPOSE_CMD=()

resolve_docker_cmd() {
  local use_sudo="${1:-}"
  DOCKER_COMPOSE_CMD=()
  if [[ -n "$use_sudo" ]]; then
    if sudo docker compose version &>/dev/null 2>&1; then
      DOCKER_COMPOSE_CMD=(sudo docker compose)
    elif sudo docker-compose version &>/dev/null 2>&1; then
      DOCKER_COMPOSE_CMD=(sudo docker-compose)
    fi
  else
    if docker compose version &>/dev/null 2>&1; then
      DOCKER_COMPOSE_CMD=(docker compose)
    elif docker-compose version &>/dev/null 2>&1; then
      DOCKER_COMPOSE_CMD=(docker-compose)
    fi
  fi
}

DC() { "${DOCKER_COMPOSE_CMD[@]}" --project-directory "$INSTALL_DIR" "$@"; }
DK() { ${DOCKER_SUDO:+sudo} docker "$@"; }

# ── Docker installieren ────────────────────────────────────────────
install_docker() {
  log "Docker-Installation gestartet ($OS_ID $OS_VERSION)"
  case "$OS_FAMILY" in
    debian)
      sudo apt-get update -qq >> "$LOG_FILE" 2>&1
      sudo apt-get install -y -qq ca-certificates curl gnupg lsb-release >> "$LOG_FILE" 2>&1
      sudo install -m 0755 -d /etc/apt/keyrings
      curl -fsSL "https://download.docker.com/linux/${OS_ID}/gpg" \
        | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg 2>>"$LOG_FILE"
      sudo chmod a+r /etc/apt/keyrings/docker.gpg
      echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
https://download.docker.com/linux/${OS_ID} $(lsb_release -cs) stable" \
        | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
      sudo apt-get update -qq >> "$LOG_FILE" 2>&1
      sudo apt-get install -y -qq docker-ce docker-ce-cli containerd.io \
        docker-buildx-plugin docker-compose-plugin >> "$LOG_FILE" 2>&1
      ;;
    rhel)
      sudo dnf install -y -q dnf-plugins-core >> "$LOG_FILE" 2>&1
      sudo dnf config-manager --add-repo \
        https://download.docker.com/linux/centos/docker-ce.repo >> "$LOG_FILE" 2>&1
      sudo dnf install -y -q docker-ce docker-ce-cli containerd.io \
        docker-buildx-plugin docker-compose-plugin >> "$LOG_FILE" 2>&1
      sudo systemctl enable --now docker >> "$LOG_FILE" 2>&1
      ;;
  esac
  log "Docker-Installation abgeschlossen."
}

# ================================================================
#  MAIN
# ================================================================
banner
echo -e "  ${BLD}Willkommen beim ReviGuard Installations-Assistenten.${RST}"
echo -e "  Dieser Assistent konfiguriert und installiert ReviGuard"
echo -e "  vollständig automatisch auf diesem Server."
echo -e "  ${DIM}Installations-Protokoll: $LOG_FILE${RST}"
echo

detect_os
log "========== ReviGuard Installation v$REVIGUARD_VERSION =========="
log "System: $OS_NAME"
log "Skript-Verzeichnis: $SCRIPT_DIR"

# ════════════════════════════════════════════════════════════════
step "Systemberechtigungen prüfen"
# ════════════════════════════════════════════════════════════════

if [[ "$EUID" -eq 0 ]]; then
  echo
  echo -e "  ${YLW}${BLD}┌─────────────────────────────────────────────────────────┐${RST}"
  echo -e "  ${YLW}${BLD}│  Hinweis: Sie sind als root angemeldet                  │${RST}"
  echo -e "  ${YLW}${BLD}└─────────────────────────────────────────────────────────┘${RST}"
  echo
  echo -e "  Für eine sichere Installation wird empfohlen, einen"
  echo -e "  normalen Benutzer mit sudo-Rechten zu verwenden."
  echo
  echo -e "  ${BLD}Neuen Benutzer anlegen (Befehle als root ausführen):${RST}"
  echo
  echo -e "  ${CYN}1)${RST} Benutzer anlegen und sudo-Rechte vergeben:"
  echo -e "     ${DIM}adduser BENUTZERNAME${RST}"
  echo -e "     ${DIM}usermod -aG sudo BENUTZERNAME${RST}"
  echo
  echo -e "  ${CYN}2)${RST} Als neuen Benutzer neu anmelden:"
  echo -e "     ${DIM}su - BENUTZERNAME${RST}"
  echo
  echo -e "  ${CYN}3)${RST} Installation erneut starten:"
  echo -e "     ${DIM}curl -fsSL https://raw.githubusercontent.com/sushiflux/reviguard_install/main/get.sh | bash${RST}"
  echo
  ask_yn "Trotzdem als root fortfahren?" "n" \
    || die "Installation abgebrochen. Bitte Benutzer anlegen und neu starten."
  INSTALL_AS_ROOT=true
  CURRENT_USER="root"
else
  INSTALL_AS_ROOT=false
  CURRENT_USER="$(whoami)"
  ok "Angemeldet als: ${BLD}$CURRENT_USER${RST}"
  if ! sudo -n true 2>/dev/null; then
    info "Bitte sudo-Passwort eingeben:"
    sudo -v || die "Keine sudo-Berechtigung vorhanden."
  fi
  ok "sudo-Berechtigung bestätigt."
fi

# ════════════════════════════════════════════════════════════════
step "Systemanforderungen prüfen"
# ════════════════════════════════════════════════════════════════

echo
info "Betriebssystem:   ${BLD}$OS_NAME${RST}"

# Verfügbarer Speicherplatz (in MB)
AVAIL_DISK=$(df -m "$SCRIPT_DIR" 2>/dev/null | awk 'NR==2{print $4}' || echo 0)
if [[ "$AVAIL_DISK" -lt "$MIN_DISK_MB" ]]; then
  die "Zu wenig Festplattenspeicher. Benötigt: ${MIN_DISK_MB} MB, Verfügbar: ${AVAIL_DISK} MB."
fi
ok "Festplatte:        ${BLD}${AVAIL_DISK} MB${RST} verfügbar (Minimum: ${MIN_DISK_MB} MB)"

# Verfügbarer RAM (in MB)
AVAIL_RAM=$(free -m 2>/dev/null | awk '/^Mem:/{print $7}' || echo 9999)
if [[ "$AVAIL_RAM" -lt "$MIN_RAM_MB" ]]; then
  warn "Wenig freier RAM: ${AVAIL_RAM} MB. Empfohlen: mindestens ${MIN_RAM_MB} MB."
else
  ok "Arbeitsspeicher:  ${BLD}${AVAIL_RAM} MB${RST} frei"
fi

log "Speicher: ${AVAIL_DISK}MB Disk, ${AVAIL_RAM}MB RAM"

# ════════════════════════════════════════════════════════════════
step "Docker und Abhängigkeiten prüfen"
# ════════════════════════════════════════════════════════════════
echo

NEED_DOCKER=false; NEED_COMPOSE=false; DOCKER_SUDO=""

# Docker prüfen
if command -v docker &>/dev/null; then
  if docker info &>/dev/null 2>&1; then
    DOCKER_VER=$(docker --version | grep -oP '[\d.]+' | head -1)
    ok "Docker v${BLD}${DOCKER_VER}${RST} — bereit"
  elif sudo docker info &>/dev/null 2>&1; then
    DOCKER_VER=$(docker --version | grep -oP '[\d.]+' | head -1)
    warn "Docker v${DOCKER_VER} gefunden — Benutzer noch nicht in docker-Gruppe (wird konfiguriert)"
    DOCKER_SUDO="sudo"
  else
    warn "Docker gefunden, aber nicht erreichbar — Neuinstallation erforderlich"
    NEED_DOCKER=true
  fi
else
  warn "Docker:           ${RED}nicht installiert${RST}"
  NEED_DOCKER=true
fi

# Docker Compose prüfen
resolve_docker_cmd "$DOCKER_SUDO"
if [[ ${#DOCKER_COMPOSE_CMD[@]} -eq 0 ]]; then
  warn "Docker Compose:   ${RED}nicht installiert${RST}"
  NEED_COMPOSE=true
else
  ok "Docker Compose    — bereit"
fi

# curl sicherstellen
if ! command -v curl &>/dev/null; then
  case "$OS_FAMILY" in
    debian) sudo apt-get install -y -qq curl >> "$LOG_FILE" 2>&1 ;;
    rhel)   sudo dnf install -y -q  curl >> "$LOG_FILE" 2>&1 ;;
  esac
fi

# ════════════════════════════════════════════════════════════════
step "Fehlende Komponenten installieren"
# ════════════════════════════════════════════════════════════════

if $NEED_DOCKER || $NEED_COMPOSE; then
  echo
  echo -e "  ${YLW}${BLD}Folgende Komponenten sind nicht installiert:${RST}"
  echo
  $NEED_DOCKER  && echo -e "    ${YLW}•${RST} Docker Engine"
  $NEED_COMPOSE && echo -e "    ${YLW}•${RST} Docker Compose Plugin"
  echo

  ask_yn "Jetzt automatisch installieren?" "j" \
    || die "Ohne Docker kann ReviGuard nicht betrieben werden."

  sep; echo
  echo -e "  ${BLD}Docker-Benutzer konfigurieren:${RST}"
  echo
  echo -e "  Docker-Befehle werden einem Benutzer zugeordnet."
  echo -e "  Dieser Benutzer kann Docker ohne 'sudo' verwenden."
  echo

  DOCKER_USER=$(ask "Benutzername" "${SUDO_USER:-$CURRENT_USER}")
  id "$DOCKER_USER" &>/dev/null || die "Benutzer '$DOCKER_USER' existiert nicht."

  echo
  spinner_start "Docker wird installiert..."
  install_docker
  spinner_stop
  ok "Docker installiert."

  resolve_docker_cmd ""
  if [[ ${#DOCKER_COMPOSE_CMD[@]} -eq 0 ]]; then resolve_docker_cmd "sudo"; fi
  if [[ ${#DOCKER_COMPOSE_CMD[@]} -eq 0 ]]; then die "Docker Compose konnte nicht gefunden werden."; fi

  # Benutzer zur docker-Gruppe hinzufügen
  if ! groups "$DOCKER_USER" 2>/dev/null | grep -q '\bdocker\b'; then
    sudo usermod -aG docker "$DOCKER_USER"
    ok "Benutzer '${DOCKER_USER}' zur docker-Gruppe hinzugefügt."
    DOCKER_SUDO="sudo"
    resolve_docker_cmd "sudo"
    warn "Nach der Installation bitte einmal ab- und neu anmelden,"
    warn "damit Docker ohne 'sudo' nutzbar ist."
  else
    ok "Benutzer '${DOCKER_USER}' ist bereits in der docker-Gruppe."
    DOCKER_SUDO=""
    resolve_docker_cmd ""
  fi
else
  ok "Alle Komponenten sind vorhanden."
  DOCKER_USER="${SUDO_USER:-$CURRENT_USER}"
fi

log "Docker-Benutzer: $DOCKER_USER | DOCKER_COMPOSE_CMD: ${DOCKER_COMPOSE_CMD[*]}"

# ════════════════════════════════════════════════════════════════
step "Installationsverzeichnis festlegen"
# ════════════════════════════════════════════════════════════════
echo
echo -e "  ReviGuard wird in ein Zielverzeichnis installiert."
echo -e "  ${DIM}Empfohlen: /opt/reviguard (Standard für Server-Software)${RST}"
echo

INSTALL_DIR=$(ask "Installationsverzeichnis" "/opt/reviguard")

# Prüfen ob bereits installiert (Upgrade-Erkennung)
if [[ -f "$INSTALL_DIR/.env" ]] && [[ -f "$INSTALL_DIR/artisan" ]]; then
  INSTALLED_VERSION=$(grep -oP '(?<=APP_VERSION=)[\d.]+' "$INSTALL_DIR/.env" 2>/dev/null || echo "unbekannt")
  echo
  warn "${BLD}ReviGuard ist bereits installiert!${RST}"
  warn "Installierte Version: ${BLD}v${INSTALLED_VERSION}${RST}"
  warn "Neue Version:         ${BLD}v${REVIGUARD_VERSION}${RST}"
  echo
  echo -e "  ${BLD}Wie möchten Sie fortfahren?${RST}"
  echo
  echo -e "  ${CYN}1)${RST} Upgrade durchführen ${DIM}(Daten bleiben erhalten)${RST}"
  echo -e "  ${CYN}2)${RST} Neuinstallation ${DIM}(${RED}alle Daten werden gelöscht${RST}${DIM})${RST}"
  echo -e "  ${CYN}3)${RST} Abbrechen"
  echo
  UPGRADE_CHOICE=$(ask "Auswahl" "1")
  case "$UPGRADE_CHOICE" in
    1) IS_UPGRADE=true ;;
    2)
      ask_yn "${RED}${BLD}Wirklich alle Daten löschen?${RST} Diese Aktion ist nicht umkehrbar!" "n" \
        || die "Neuinstallation abgebrochen."
      IS_UPGRADE=false
      ;;
    *) die "Installation abgebrochen." ;;
  esac
else
  IS_UPGRADE=false
fi

# Zielverzeichnis erstellen und Dateien kopieren
if ! $IS_UPGRADE; then
  if [[ "$INSTALL_DIR" != "$SCRIPT_DIR" ]]; then
    echo
    spinner_start "Dateien werden nach $INSTALL_DIR kopiert..."
    sudo mkdir -p "$INSTALL_DIR"
    sudo chown "$(id -u):$(id -g)" "$INSTALL_DIR"
    sudo chmod 755 "$INSTALL_DIR"
    rsync -a --exclude='.env' --exclude='vendor/' \
      --exclude='.git/' \
      --exclude='storage/logs/*' --exclude='storage/framework/cache/*' \
      --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*' \
      --exclude='bootstrap/cache/*' --exclude='install.log' \
      "$SCRIPT_DIR/" "$INSTALL_DIR/"
    spinner_stop
    ok "Dateien kopiert nach: ${BLD}$INSTALL_DIR${RST}"
  else
    ok "Installation im aktuellen Verzeichnis: ${BLD}$INSTALL_DIR${RST}"
  fi
fi

log "Installationsverzeichnis: $INSTALL_DIR | Upgrade: $IS_UPGRADE"

# ════════════════════════════════════════════════════════════════
step "Datenbank konfigurieren"
# ════════════════════════════════════════════════════════════════
echo

if $IS_UPGRADE; then
  # Bestehende DB-Konfiguration übernehmen
  DB_HOST=$(grep -oP '(?<=DB_HOST=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "db")
  DB_PORT=$(grep -oP '(?<=DB_PORT=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "3306")
  DB_DATABASE=$(grep -oP '(?<=DB_DATABASE=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "reviguard")
  DB_USERNAME=$(grep -oP '(?<=DB_USERNAME=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "appuser")
  DB_PASSWORD=$(grep -oP '(?<=DB_PASSWORD=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "")
  MYSQL_ROOT_PASSWORD=$(grep -oP '(?<=MYSQL_ROOT_PASSWORD=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "")
  [[ "$DB_HOST" == "db" ]] && DB_MODE="local" || DB_MODE="external"
  MYSQL_DATABASE=$(grep -oP '(?<=MYSQL_DATABASE=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "")
  MYSQL_USER=$(grep -oP '(?<=MYSQL_USER=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "")
  MYSQL_PASSWORD="$DB_PASSWORD"
  info "Bestehende Datenbank-Konfiguration wird verwendet."
  ok "Datenbank: ${BLD}$DB_HOST:$DB_PORT/$DB_DATABASE${RST}"
else
  echo -e "  ${BLD}Datenbanktyp wählen:${RST}"
  echo
  echo -e "  ${CYN}1)${RST} Lokaler Docker-Container  ${DIM}(empfohlen — kein MySQL nötig)${RST}"
  echo -e "  ${CYN}2)${RST} Externer MySQL/MariaDB-Server"
  echo
  DB_CHOICE=$(ask "Auswahl" "1")

  if [[ "$DB_CHOICE" == "2" ]]; then
    # ── Externer DB-Server ──────────────────────────────────────
    DB_MODE="external"
    sep; echo
    echo -e "  ${BLD}Verbindungsdaten — Externer MySQL-Server:${RST}"
    echo
    DB_HOST=$(ask     "Hostname / IP"        "localhost")
    DB_PORT=$(ask     "Port"                 "3306")
    echo
    echo -e "  ${BLD}Administrator-Zugangsdaten${RST} ${DIM}(einmalig für DB-Einrichtung):${RST}"
    DB_ROOT_USER=$(ask    "Admin-Benutzername"   "root")
    DB_ROOT_PASS=$(ask_secret "Admin-Passwort")
    echo
    echo -e "  ${BLD}ReviGuard-Datenbankeinstellungen:${RST}"
    DB_DATABASE=$(ask     "Datenbankname"         "reviguard")
    DB_USERNAME=$(ask     "Datenbank-Benutzer"    "reviguard_user")
    DB_PASSWORD=$(ask_secret "Passwort für '$DB_USERNAME' [Enter = automatisch]")
    if [[ -z "$DB_PASSWORD" ]]; then DB_PASSWORD=$(gen_password); info "Passwort automatisch generiert."; fi

    # Port-Erreichbarkeit testen (schnell, ohne Docker)
    sep
    info "Teste Erreichbarkeit von ${BLD}$DB_HOST:$DB_PORT${RST}..."
    if ! bash -c "echo > /dev/tcp/$DB_HOST/$DB_PORT" &>/dev/null 2>&1; then
      die "Port $DB_PORT auf $DB_HOST nicht erreichbar. Firewall oder Hostname prüfen."
    fi
    ok "Server erreichbar."

    # MySQL-Verbindung testen
    spinner_start "Teste MySQL-Anmeldung..."
    if ! DK run --rm mysql:8.0 \
        mysql -h "$DB_HOST" -P "$DB_PORT" \
        -u "$DB_ROOT_USER" -p"$DB_ROOT_PASS" \
        --connect-timeout=10 \
        -e "SELECT 1;" >> "$LOG_FILE" 2>&1; then
      spinner_stop
      die "MySQL-Anmeldung fehlgeschlagen. Zugangsdaten prüfen."
    fi
    spinner_stop
    ok "MySQL-Verbindung erfolgreich."

    # Datenbank und Benutzer anlegen
    spinner_start "Datenbank und Benutzer werden angelegt..."
    DK run --rm mysql:8.0 mysql \
      -h "$DB_HOST" -P "$DB_PORT" \
      -u "$DB_ROOT_USER" -p"$DB_ROOT_PASS" \
      -e "
        CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\`
          CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
        CREATE USER IF NOT EXISTS '$DB_USERNAME'@'%' IDENTIFIED BY '$DB_PASSWORD';
        GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'%';
        FLUSH PRIVILEGES;
      " >> "$LOG_FILE" 2>&1
    spinner_stop
    ok "Datenbank '${BLD}$DB_DATABASE${RST}' und Benutzer '${BLD}$DB_USERNAME${RST}' angelegt."

    MYSQL_DATABASE=""; MYSQL_USER=""; MYSQL_PASSWORD=""; MYSQL_ROOT_PASSWORD=""
  else
    # ── Lokale Docker-Datenbank ─────────────────────────────────
    DB_MODE="local"; DB_HOST="db"; DB_PORT="3306"
    sep; echo
    echo -e "  ${BLD}Lokale MySQL-Datenbank (Docker):${RST}"
    echo
    DB_DATABASE=$(ask "Datenbankname"       "reviguard")
    DB_USERNAME=$(ask "Datenbank-Benutzer"  "appuser")
    DB_PASSWORD=$(ask_secret "Datenbank-Passwort [Enter = automatisch]")
    if [[ -z "$DB_PASSWORD" ]]; then DB_PASSWORD=$(gen_password); info "Passwort automatisch generiert."; fi
    DB_ROOT_PASS=$(gen_password)
    info "MySQL root-Passwort automatisch generiert."

    MYSQL_DATABASE="$DB_DATABASE"
    MYSQL_USER="$DB_USERNAME"
    MYSQL_PASSWORD="$DB_PASSWORD"
    MYSQL_ROOT_PASSWORD="$DB_ROOT_PASS"
  fi
fi

ok "Datenbank-Konfiguration abgeschlossen."
log "DB-Modus: $DB_MODE | Host: $DB_HOST:$DB_PORT | DB: $DB_DATABASE"

# ════════════════════════════════════════════════════════════════
step "Anwendung konfigurieren"
# ════════════════════════════════════════════════════════════════
echo

if $IS_UPGRADE; then
  APP_PORT=$(grep -oP '(?<=APP_PORT=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "80")
  APP_URL=$(grep -oP '(?<=APP_URL=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "")
  PMA_PORT=$(grep -oP '(?<=PMA_PORT=)\S+' "$INSTALL_DIR/.env" 2>/dev/null || echo "8085")
  PMA_ENABLED=false
  info "Bestehende App-Konfiguration wird verwendet."
  ok "URL: ${BLD}$APP_URL${RST}  |  Port: ${BLD}$APP_PORT${RST}"
else
  # Automatisch Server-IP ermitteln
  AUTO_IP=$(hostname -I 2>/dev/null | awk '{print $1}' | tr -d ' ') || AUTO_IP="localhost"

  # Port-Verfügbarkeit prüfen
  check_port() {
    local port="$1"
    if ss -tlnp 2>/dev/null | grep -q ":${port} " || \
       netstat -tlnp 2>/dev/null | grep -q ":${port} "; then
      echo "belegt"
    else
      echo "frei"
    fi
  }

  echo -e "  ${BLD}Netzwerk-Einstellungen:${RST}"
  echo

  PORT_80_STATUS=$(check_port 80)
  if [[ "$PORT_80_STATUS" == "belegt" ]]; then
    warn "Port 80 ist bereits belegt."
    APP_PORT=$(ask "HTTP-Port für ReviGuard" "8080")
  else
    APP_PORT=$(ask "HTTP-Port für ReviGuard" "80")
  fi

  # Port nochmals prüfen
  if [[ $(check_port "$APP_PORT") == "belegt" ]]; then
    warn "Port $APP_PORT ist belegt. Installation fortsetzen?"
    ask_yn "Trotzdem fortfahren?" "n" || die "Bitte einen freien Port wählen."
  fi

  if [[ "$APP_PORT" == "80" ]]; then
    APP_URL=$(ask "Öffentliche URL" "http://${AUTO_IP}")
  else
    APP_URL=$(ask "Öffentliche URL" "http://${AUTO_IP}:${APP_PORT}")
  fi

  echo
  PMA_ENABLED=false; PMA_PORT="8085"
  if ask_yn "phpMyAdmin (Datenbank-Verwaltung) installieren?" "n"; then
    PMA_ENABLED=true
    PMA_PORT=$(ask "Port für phpMyAdmin" "8085")
    if [[ $(check_port "$PMA_PORT") == "belegt" ]]; then
      warn "Port $PMA_PORT ist belegt. Bitte nach der Installation anpassen."
    fi
  fi
fi

ok "Anwendungs-Konfiguration abgeschlossen."
log "App-Port: $APP_PORT | URL: $APP_URL | phpMyAdmin: $PMA_ENABLED"

# ════════════════════════════════════════════════════════════════
step "System-Benutzer und Berechtigungen einrichten"
# ════════════════════════════════════════════════════════════════
echo

SVC_USER="reviguard"

# System-Benutzer anlegen (kein Login, kein Home)
if id "$SVC_USER" &>/dev/null 2>&1; then
  ok "System-Benutzer '${BLD}$SVC_USER${RST}' existiert bereits."
else
  sudo useradd --system --no-create-home --shell /usr/sbin/nologin "$SVC_USER"
  ok "System-Benutzer '${BLD}$SVC_USER${RST}' angelegt."
fi

# Service-Benutzer zur docker-Gruppe hinzufügen
if ! groups "$SVC_USER" 2>/dev/null | grep -q '\bdocker\b'; then
  sudo usermod -aG docker "$SVC_USER"
  ok "Benutzer '$SVC_USER' zur docker-Gruppe hinzugefügt."
fi

SVC_UID=$(id -u "$SVC_USER")
SVC_GID=$(id -g "$SVC_USER")

# Verzeichnis-Eigentümer setzen
sudo chown -R "$SVC_USER:$SVC_USER" "$INSTALL_DIR"
sudo chmod 755 "$INSTALL_DIR"                                    # traversierbar für alle
sudo find "$INSTALL_DIR" -type d -exec sudo chmod 755 {} \;     # alle Unterordner
sudo find "$INSTALL_DIR" -type f -exec sudo chmod 644 {} \;     # alle Dateien
sudo chmod 755 "$INSTALL_DIR/artisan"                           # ausführbar
sudo chmod -R 775 "$INSTALL_DIR/storage" "$INSTALL_DIR/bootstrap/cache"
sudo chmod 640 "$INSTALL_DIR/.env" 2>/dev/null || true          # .env absichern
ok "Verzeichnis-Berechtigungen gesetzt."

log "Service-User: $SVC_USER (UID=$SVC_UID, GID=$SVC_GID)"

# ════════════════════════════════════════════════════════════════
step ".env Konfigurationsdatei erstellen"
# ════════════════════════════════════════════════════════════════

if ! $IS_UPGRADE; then
  sudo tee "$INSTALL_DIR/.env" > /dev/null <<EOF
APP_NAME=ReviGuard
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=${APP_URL}
APP_VERSION=${REVIGUARD_VERSION}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

MYSQL_DATABASE=${MYSQL_DATABASE}
MYSQL_USER=${MYSQL_USER:-}
MYSQL_PASSWORD=${MYSQL_PASSWORD:-}
MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-}

HOST_UID=${SVC_UID}
HOST_GID=${SVC_GID}
APP_PORT=${APP_PORT}
PMA_PORT=${PMA_PORT}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
BCRYPT_ROUNDS=12
EOF
  sudo chown "$SVC_USER:$SVC_USER" "$INSTALL_DIR/.env"
  sudo chmod 640 "$INSTALL_DIR/.env"
  ok ".env erstellt und gesichert (640)."
fi

# docker-compose.override.yml
OVERRIDE="$INSTALL_DIR/docker-compose.override.yml"

if ! $IS_UPGRADE; then
  if [[ "$DB_MODE" == "external" ]] && $PMA_ENABLED; then
    sudo tee "$OVERRIDE" > /dev/null <<EOF
services:
  phpmyadmin:
    environment:
      PMA_HOST: ${DB_HOST}
      PMA_PORT: ${DB_PORT}
      MYSQL_ROOT_PASSWORD: ""
EOF
  elif ! $PMA_ENABLED; then
    sudo tee "$OVERRIDE" > /dev/null <<EOF
services:
  phpmyadmin:
    profiles:
      - disabled
EOF
  fi
fi

ok "Docker Compose Konfiguration abgeschlossen."

# ════════════════════════════════════════════════════════════════
step "Docker-Container bauen und starten"
# ════════════════════════════════════════════════════════════════
echo

spinner_start "Container werden gebaut (kann einige Minuten dauern)..."
if [[ "$DB_MODE" == "external" ]]; then
  if $PMA_ENABLED; then
    DC up -d --build --no-deps php web phpmyadmin >> "$LOG_FILE" 2>&1
  else
    DC up -d --build --no-deps php web >> "$LOG_FILE" 2>&1
  fi
else
  if $PMA_ENABLED; then
    DC up -d --build >> "$LOG_FILE" 2>&1
  else
    DC up -d --build php web db >> "$LOG_FILE" 2>&1
  fi
fi
spinner_stop
ok "Container gestartet."

# Warten bis PHP bereit
spinner_start "Warte auf PHP-Container..."
for i in $(seq 1 40); do
  if DK exec reviguard_php php -r "echo 'ok';" &>/dev/null 2>&1; then break; fi
  sleep 3
  if [[ $i -eq 40 ]]; then spinner_stop; die "PHP-Container antwortet nicht. Log: docker logs reviguard_php"; fi
done
spinner_stop
ok "PHP-Container bereit."

# Warten auf MySQL (nur lokal)
if [[ "$DB_MODE" == "local" ]]; then
  spinner_start "Warte auf MySQL-Datenbank..."
  for i in $(seq 1 40); do
    if DK exec reviguard_db mysqladmin ping \
        -u root -p"$MYSQL_ROOT_PASSWORD" --silent &>/dev/null 2>&1; then break; fi
    sleep 3
    if [[ $i -eq 40 ]]; then spinner_stop; die "MySQL antwortet nicht. Log: docker logs reviguard_db"; fi
  done
  spinner_stop
  ok "MySQL bereit."
fi

# ════════════════════════════════════════════════════════════════
step "Anwendung einrichten"
# ════════════════════════════════════════════════════════════════
echo

spinner_start "PHP-Abhängigkeiten installieren..."
DK exec reviguard_php composer install \
  --no-dev --optimize-autoloader --no-interaction --quiet >> "$LOG_FILE" 2>&1
spinner_stop; ok "Composer-Abhängigkeiten installiert."

if ! $IS_UPGRADE; then
  spinner_start "Anwendungsschlüssel generieren..."
  DK exec reviguard_php php artisan key:generate --force >> "$LOG_FILE" 2>&1
  spinner_stop; ok "APP_KEY generiert."
fi

spinner_start "Dateiberechtigungen setzen..."
DK exec -u root reviguard_php bash -c "
  mkdir -p /var/www/html/storage/logs \
           /var/www/html/storage/framework/cache \
           /var/www/html/storage/framework/sessions \
           /var/www/html/storage/framework/views \
           /var/www/html/bootstrap/cache &&
  chown -R appuser:appgroup /var/www/html/storage /var/www/html/bootstrap/cache &&
  chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
" >> "$LOG_FILE" 2>&1
spinner_stop; ok "Dateiberechtigungen gesetzt."

spinner_start "Datenbank-Migrationen ausführen..."
DK exec reviguard_php php artisan migrate --force >> "$LOG_FILE" 2>&1
spinner_stop; ok "Migrationen abgeschlossen."

if ! $IS_UPGRADE; then
  spinner_start "Administrator-Account anlegen..."
  DK exec reviguard_php php artisan db:seed --force >> "$LOG_FILE" 2>&1
  spinner_stop; ok "Administrator-Account angelegt."
fi

spinner_start "Caches optimieren..."
DK exec reviguard_php php artisan config:cache >> "$LOG_FILE" 2>&1
DK exec reviguard_php php artisan route:cache  >> "$LOG_FILE" 2>&1
DK exec reviguard_php php artisan view:cache   >> "$LOG_FILE" 2>&1
spinner_stop; ok "Caches optimiert."

# ════════════════════════════════════════════════════════════════
step "Systemd-Service einrichten (Autostart)"
# ════════════════════════════════════════════════════════════════
echo

DOCKER_BIN=$(which docker)
SERVICE_FILE="/etc/systemd/system/reviguard.service"

sudo tee "$SERVICE_FILE" > /dev/null <<EOF
[Unit]
Description=ReviGuard Versions-Management System
Documentation=https://reviguard.de
After=network-online.target docker.service
Requires=docker.service
Wants=network-online.target

[Service]
Type=oneshot
RemainAfterExit=yes
User=${SVC_USER}
Group=${SVC_USER}
WorkingDirectory=${INSTALL_DIR}

ExecStart=${DOCKER_BIN} compose -f ${INSTALL_DIR}/docker-compose.yml --env-file ${INSTALL_DIR}/.env up -d
ExecStop=${DOCKER_BIN} compose -f ${INSTALL_DIR}/docker-compose.yml --env-file ${INSTALL_DIR}/.env stop
ExecReload=${DOCKER_BIN} compose -f ${INSTALL_DIR}/docker-compose.yml --env-file ${INSTALL_DIR}/.env restart

TimeoutStartSec=300
TimeoutStopSec=60
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable reviguard >> "$LOG_FILE" 2>&1
ok "Systemd-Service 'reviguard' registriert."
ok "ReviGuard startet ab sofort automatisch beim Server-Boot."
log "Systemd-Service erstellt: $SERVICE_FILE"

# ════════════════════════════════════════════════════════════════
step "Management- und Deinstallations-Skript erstellen"
# ════════════════════════════════════════════════════════════════
echo

# Management-Skript
sudo tee "$INSTALL_DIR/reviguard.sh" > /dev/null <<MGMT
#!/usr/bin/env bash
# ================================================================
#  ReviGuard — Management-Skript
#  Verwendung: ./reviguard.sh <Befehl>
# ================================================================
set -euo pipefail
INSTALL_DIR="\$(cd "\$(dirname "\${BASH_SOURCE[0]}")" && pwd)"
cd "\$INSTALL_DIR"

DK()  { docker exec reviguard_php "\$@"; }
ART() { docker exec reviguard_php php artisan "\$@"; }

case "\${1:-help}" in
  start)
    sudo systemctl start reviguard
    echo "ReviGuard gestartet."
    ;;
  stop)
    sudo systemctl stop reviguard
    echo "ReviGuard gestoppt."
    ;;
  restart)
    sudo systemctl restart reviguard
    echo "ReviGuard neu gestartet."
    ;;
  status)
    echo ""
    sudo systemctl status reviguard --no-pager
    echo ""
    docker compose --project-directory "\$INSTALL_DIR" ps
    ;;
  update)
    GITHUB_USER="sushiflux"
    GITHUB_REPO="reviguard_install"
    CURRENT=\$(grep -oP '(?<=APP_VERSION=)[\d.]+' "\$INSTALL_DIR/.env" 2>/dev/null || echo "0.0.0")

    echo "  Aktuelle Version: v\$CURRENT"
    echo "  Prüfe auf neue Version..."

    API_URL="https://api.github.com/repos/\$GITHUB_USER/\$GITHUB_REPO/releases/latest"
    LATEST=\$(curl -fsSL "\$API_URL" 2>/dev/null \
      | grep '"tag_name"' | head -1 \
      | sed 's/.*"v\([^"]*\)".*/\1/')

    if [[ -z "\$LATEST" ]]; then
      echo "  Fehler: GitHub-API nicht erreichbar oder kein Release gefunden." >&2
      exit 1
    fi

    echo "  Neueste Version:  v\$LATEST"

    if [[ "\$CURRENT" == "\$LATEST" ]]; then
      echo "  ReviGuard ist bereits aktuell."
      exit 0
    fi

    echo "  Update v\$CURRENT → v\$LATEST wird durchgeführt..."

    TMPDIR=\$(mktemp -d)
    trap "rm -rf \$TMPDIR" EXIT

    ARCHIVE="reviguard-v\${LATEST}.tar.gz"
    CHECKSUM="reviguard-v\${LATEST}.sha256"
    BASE_URL="https://github.com/\$GITHUB_USER/\$GITHUB_REPO/releases/download/v\${LATEST}"

    echo "  Lade Archiv herunter..."
    curl -fsSL "\$BASE_URL/\$ARCHIVE"  -o "\$TMPDIR/\$ARCHIVE"
    curl -fsSL "\$BASE_URL/\$CHECKSUM" -o "\$TMPDIR/\$CHECKSUM" 2>/dev/null || true

    if [[ -f "\$TMPDIR/\$CHECKSUM" ]]; then
      echo "  Prüfsumme wird verifiziert..."
      (cd "\$TMPDIR" && sed "s|reviguard-v.*\.tar\.gz|\$ARCHIVE|" "\$CHECKSUM" | sha256sum -c --status) \
        || { echo "  Fehler: Prüfsumme stimmt nicht überein!" >&2; exit 1; }
      echo "  Prüfsumme OK."
    fi

    echo "  Dateien werden entpackt..."
    tar -xzf "\$TMPDIR/\$ARCHIVE" -C "\$TMPDIR"

    echo "  Dateien werden eingespielt..."
    sudo rsync -a \
      --exclude='.env' \
      --exclude='vendor/' \
      --exclude='storage/' \
      --exclude='bootstrap/cache/' \
      --exclude='docker-compose.override.yml' \
      "\$TMPDIR/reviguard/" "\$INSTALL_DIR/"

    sudo chown -R reviguard:reviguard "\$INSTALL_DIR"
    sudo chmod 755 "\$INSTALL_DIR"
    sudo chmod 640 "\$INSTALL_DIR/.env"

    # APP_VERSION in .env aktualisieren
    sudo sed -i "s/APP_VERSION=.*/APP_VERSION=\$LATEST/" "\$INSTALL_DIR/.env"

    echo "  Abhängigkeiten installieren..."
    DK composer install --no-dev --optimize-autoloader --no-interaction --quiet

    echo "  Datenbank-Migrationen..."
    ART migrate --force

    echo "  Caches aktualisieren..."
    ART config:cache
    ART route:cache
    ART view:cache

    echo ""
    echo "  Update auf v\$LATEST erfolgreich abgeschlossen."
    ;;
  logs)
    shift
    docker compose logs -f "\${@:-}"
    ;;
  artisan)
    shift
    ART "\$@"
    ;;
  backup)
    BACKUP="\$INSTALL_DIR/backups/backup_\$(date '+%Y%m%d_%H%M%S').sql.gz"
    mkdir -p "\$INSTALL_DIR/backups"
    docker exec reviguard_db sh -c \
      'mysqldump -u "\$MYSQL_USER" -p"\$MYSQL_PASSWORD" "\$MYSQL_DATABASE"' \
      | gzip > "\$BACKUP"
    echo "Backup gespeichert: \$BACKUP"
    ;;
  help|*)
    echo ""
    echo "  ReviGuard v\$(grep APP_VERSION .env 2>/dev/null | cut -d= -f2 || echo '?')"
    echo "  ──────────────────────────────────────────────────────"
    echo "  start            App starten"
    echo "  stop             App stoppen"
    echo "  restart          App neu starten"
    echo "  status           Status und Container anzeigen"
    echo "  update           Updates einspielen"
    echo "  logs [service]   Log-Ausgabe verfolgen"
    echo "  artisan <cmd>    Laravel Artisan-Befehl ausführen"
    echo "  backup           Datenbank-Backup erstellen"
    echo ""
    ;;
esac
MGMT
sudo chmod +x "$INSTALL_DIR/reviguard.sh"
ok "Management-Skript erstellt: ${BLD}$INSTALL_DIR/reviguard.sh${RST}"

# Deinstallations-Skript
sudo tee "$INSTALL_DIR/uninstall.sh" > /dev/null <<UNINSTALL
#!/usr/bin/env bash
# ================================================================
#  ReviGuard — Deinstallations-Skript
# ================================================================
set -euo pipefail
INSTALL_DIR="$INSTALL_DIR"

RED='\033[0;31m'; YLW='\033[1;33m'; GRN='\033[0;32m'; BLD='\033[1m'; RST='\033[0m'

echo
echo -e "\${RED}\${BLD}ReviGuard Deinstallation\${RST}"
echo -e "\${YLW}Diese Aktion entfernt ReviGuard vollständig von diesem Server.\${RST}"
echo

read -rp "  Bitte 'DEINSTALLIEREN' eingeben zum Bestätigen: " CONFIRM
[[ "\$CONFIRM" != "DEINSTALLIEREN" ]] && echo "Abgebrochen." && exit 0

echo
echo -e "  Stoppe Dienst..."
sudo systemctl stop reviguard 2>/dev/null || true
sudo systemctl disable reviguard 2>/dev/null || true
sudo rm -f /etc/systemd/system/reviguard.service
sudo systemctl daemon-reload

echo -e "  Entferne Docker-Container und Volumes..."
cd "\$INSTALL_DIR"
docker compose down -v 2>/dev/null || true

echo -e "  Entferne Installationsverzeichnis..."
sudo rm -rf "\$INSTALL_DIR"

echo -e "  Entferne System-Benutzer..."
sudo userdel reviguard 2>/dev/null || true

echo
echo -e "  \${GRN}ReviGuard wurde vollständig entfernt.\${RST}"
echo
UNINSTALL
sudo chmod +x "$INSTALL_DIR/uninstall.sh"
ok "Deinstallations-Skript erstellt: ${BLD}$INSTALL_DIR/uninstall.sh${RST}"

# ════════════════════════════════════════════════════════════════
#  Abschluss
# ════════════════════════════════════════════════════════════════
echo
echo -e "${GRN}${BLD}"
echo "  ╔══════════════════════════════════════════════════════════════╗"
echo "  ║                                                              ║"
if $IS_UPGRADE; then
echo "  ║       ReviGuard wurde erfolgreich aktualisiert!             ║"
else
echo "  ║       ReviGuard wurde erfolgreich installiert!              ║"
fi
echo "  ║                                                              ║"
echo "  ╚══════════════════════════════════════════════════════════════╝"
echo -e "${RST}"
echo -e "  ${BLD}Anwendung:${RST}"
echo -e "  ${CYN}${BLD}${APP_URL}${RST}"
echo

if ! $IS_UPGRADE; then
  echo -e "  ${BLD}Administrator-Login:${RST}"
  echo -e "  Benutzername:  ${BLD}RGAdmin${RST}"
  echo -e "  Passwort:      ${BLD}RGAdmin${RST}"
  echo -e "  ${YLW}⚠  Passwort nach dem ersten Login sofort ändern!${RST}"
  echo
fi

echo -e "  ${BLD}Installation:${RST}"
echo -e "  Verzeichnis:   ${DIM}$INSTALL_DIR${RST}"
echo -e "  Service:       ${DIM}systemctl [start|stop|status] reviguard${RST}"
echo -e "  Management:    ${DIM}$INSTALL_DIR/reviguard.sh [start|stop|update|backup]${RST}"
echo -e "  Deinstall:     ${DIM}$INSTALL_DIR/uninstall.sh${RST}"
echo

if ! $IS_UPGRADE; then
  echo -e "  ${BLD}Datenbank:${RST}"
  echo -e "  Host:      ${DIM}$DB_HOST:$DB_PORT${RST}"
  echo -e "  Datenbank: ${DIM}$DB_DATABASE${RST}"
  echo -e "  Benutzer:  ${DIM}$DB_USERNAME${RST}"
  echo -e "  Passwort:  ${DIM}$DB_PASSWORD${RST}"
  $PMA_ENABLED && echo -e "  phpMyAdmin: ${DIM}${APP_URL%:*}:${PMA_PORT}${RST}"
  echo
fi

if [[ -n "$DOCKER_SUDO" ]]; then
  echo -e "  ${YLW}Hinweis: Bitte einmal ab- und neu anmelden, damit Docker"
  echo -e "  für '$DOCKER_USER' ohne sudo nutzbar ist.${RST}"
  echo
fi

echo -e "  ${DIM}Installations-Protokoll: $LOG_FILE${RST}"
echo
log "========== Installation erfolgreich abgeschlossen =========="
