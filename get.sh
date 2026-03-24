#!/usr/bin/env bash
# ================================================================
#  ReviGuard — Bootstrap-Installer
#
#  Kunden führen nur folgendes aus:
#    curl -fsSL https://get.reviguard.de/install.sh | bash
#
#  Was dieser Script macht:
#    1. Prüft Systemvoraussetzungen (curl, tar)
#    2. Fragt GitHub API nach der aktuellen Version
#    3. Lädt das Release-Archiv von GitHub herunter
#    4. Verifiziert die SHA256-Prüfsumme
#    5. Entpackt und startet den Installations-Assistenten
#    6. Räumt temporäre Dateien auf
# ================================================================
set -euo pipefail

# ================================================================
#  KONFIGURATION — einmalig anpassen
# ================================================================
GITHUB_USER="sushiflux"
GITHUB_REPO="reviguard_install"
# ================================================================

GITHUB_API="https://api.github.com/repos/${GITHUB_USER}/${GITHUB_REPO}/releases/latest"
GITHUB_DOWNLOAD="https://github.com/${GITHUB_USER}/${GITHUB_REPO}/releases/download"
TEMP_DIR=""

# ── Farben ────────────────────────────────────────────────────────
RED='\033[0;31m'; GRN='\033[0;32m'; YLW='\033[1;33m'
CYN='\033[0;36m'; BLD='\033[1m'; DIM='\033[2m'; RST='\033[0m'

ok()   { echo -e "  ${GRN}✔${RST}  $*"; }
info() { echo -e "  ${CYN}ℹ${RST}  $*"; }
warn() { echo -e "  ${YLW}⚠${RST}  $*"; }
die()  { echo -e "  ${RED}✖${RST}  $*" >&2; echo; exit 1; }

cleanup() { [[ -n "$TEMP_DIR" ]] && rm -rf "$TEMP_DIR" 2>/dev/null || true; }
trap cleanup EXIT INT TERM

# ── Banner ────────────────────────────────────────────────────────
clear
echo -e "${CYN}${BLD}"
echo "  ██████╗ ███████╗██╗   ██╗██╗ ██████╗ ██╗   ██╗ █████╗ ██████╗ ██████╗ "
echo "  ██╔══██╗██╔════╝██║   ██║██║██╔════╝ ██║   ██║██╔══██╗██╔══██╗██╔══██╗"
echo "  ██████╔╝█████╗  ██║   ██║██║██║  ███╗██║   ██║███████║██████╔╝██║  ██║"
echo "  ██╔══██╗██╔══╝  ╚██╗ ██╔╝██║██║   ██║██║   ██║██╔══██║██╔══██╗██║  ██║"
echo "  ██║  ██║███████╗ ╚████╔╝ ██║╚██████╔╝╚██████╔╝██║  ██║██║  ██║██████╔╝"
echo "  ╚═╝  ╚═╝╚══════╝  ╚═══╝  ╚═╝ ╚═════╝  ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝╚═════╝ "
echo -e "${RST}"
echo -e "  ${DIM}Versions-Management System  ·  Installations-Downloader${RST}"
echo -e "  ${DIM}──────────────────────────────────────────────────────────────${RST}"
echo

# ── Voraussetzungen prüfen ────────────────────────────────────────
command -v curl &>/dev/null || die "curl fehlt. Bitte installieren: apt install curl"
command -v tar  &>/dev/null || die "tar fehlt.  Bitte installieren: apt install tar"

# ── Aktuelle Version via GitHub API ermitteln ─────────────────────
info "Ermittle aktuelle ReviGuard-Version von GitHub..."

API_RESPONSE=$(curl -fsSL --connect-timeout 15 "$GITHUB_API" 2>/dev/null) \
  || die "GitHub API nicht erreichbar. Bitte Internetverbindung prüfen."

# tag_name aus JSON lesen (ohne jq — nur bash + sed)
VERSION=$(echo "$API_RESPONSE" \
  | grep -o '"tag_name": *"[^"]*"' \
  | head -1 \
  | sed 's/.*"tag_name": *"v\{0,1\}\([^"]*\)".*/\1/')

[[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] \
  || die "Keine gültige Version von GitHub erhalten. Repository oder Release vorhanden?"

ok "Aktuelle Version: ${BLD}v${VERSION}${RST}"

# ── Download-URLs aufbauen ────────────────────────────────────────
ARCHIVE_NAME="reviguard-v${VERSION}.tar.gz"
CHECKSUM_NAME="reviguard-v${VERSION}.sha256"
ARCHIVE_URL="${GITHUB_DOWNLOAD}/v${VERSION}/${ARCHIVE_NAME}"
CHECKSUM_URL="${GITHUB_DOWNLOAD}/v${VERSION}/${CHECKSUM_NAME}"

# ── Temporäres Verzeichnis ────────────────────────────────────────
TEMP_DIR=$(mktemp -d /tmp/reviguard-XXXXXX)

# ── Archiv herunterladen ──────────────────────────────────────────
info "Lade Archiv herunter..."
echo

curl -fSL \
  --progress-bar \
  --connect-timeout 30 \
  --retry 3 \
  --location \
  "$ARCHIVE_URL" \
  -o "$TEMP_DIR/$ARCHIVE_NAME" \
  || die "Download fehlgeschlagen: $ARCHIVE_URL"

echo
ok "Download abgeschlossen."

# ── Prüfsumme verifizieren ────────────────────────────────────────
info "Verifiziere Prüfsumme (SHA256)..."

if curl -fsSL --connect-timeout 10 "$CHECKSUM_URL" \
    -o "$TEMP_DIR/$CHECKSUM_NAME" 2>/dev/null; then
  cd "$TEMP_DIR"
  if sha256sum --check "$CHECKSUM_NAME" --status 2>/dev/null; then
    ok "Prüfsumme korrekt — Archiv unverändert und sicher."
  else
    die "Prüfsummen-Fehler! Das Archiv ist möglicherweise beschädigt.
       Bitte Support kontaktieren."
  fi
  cd - > /dev/null
else
  warn "Prüfsummen-Datei nicht verfügbar — Verifikation übersprungen."
fi

# ── Entpacken ────────────────────────────────────────────────────
info "Entpacke Archiv..."

EXTRACT_DIR="$TEMP_DIR/reviguard"
mkdir -p "$EXTRACT_DIR"
tar -xzf "$TEMP_DIR/$ARCHIVE_NAME" -C "$EXTRACT_DIR" --strip-components=1 \
  || die "Entpacken fehlgeschlagen."

ok "Archiv entpackt."

# ── Installations-Skript starten ──────────────────────────────────
echo
echo -e "  ${GRN}${BLD}Starte ReviGuard Installations-Assistenten...${RST}"
echo

[[ -x "$EXTRACT_DIR/install.sh" ]] \
  || die "install.sh nicht im Archiv gefunden."

exec "$EXTRACT_DIR/install.sh" < /dev/tty
