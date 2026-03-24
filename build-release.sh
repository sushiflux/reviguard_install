#!/usr/bin/env bash
# ================================================================
#  ReviGuard — Release Builder
#
#  Erstellt ein verteilbares Release-Archiv für Kunden-Installationen.
#
#  Verwendung:
#    ./build-release.sh
#
#  Ergebnis:
#    dist/
#    ├── reviguard-v0.5.1.tar.gz   ← Archiv für Kunden
#    ├── reviguard-v0.5.1.sha256   ← Prüfsumme zur Verifikation
#    └── latest.txt                ← Aktuelle Versionsnummer
#
#  Nach dem Build: Dateien aus dist/ auf Ihren Release-Server hochladen.
# ================================================================
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# ── Farben ────────────────────────────────────────────────────────
RED='\033[0;31m'; GRN='\033[0;32m'; YLW='\033[1;33m'
CYN='\033[0;36m'; BLD='\033[1m'; DIM='\033[2m'; RST='\033[0m'

ok()   { echo -e "  ${GRN}✔${RST}  $*"; }
info() { echo -e "  ${CYN}ℹ${RST}  $*"; }
warn() { echo -e "  ${YLW}⚠${RST}  $*"; }
die()  { echo -e "  ${RED}✖${RST}  $*" >&2; exit 1; }

# ── Version aus .env lesen ────────────────────────────────────────
[[ -f ".env" ]] || die ".env nicht gefunden. Bitte aus dem ReviGuard-Projektverzeichnis starten."

VERSION=$(grep -oP '(?<=APP_VERSION=)[\d.]+' .env 2>/dev/null) \
  || die "APP_VERSION nicht in .env gefunden."

ARCHIVE_NAME="reviguard-v${VERSION}.tar.gz"
CHECKSUM_NAME="reviguard-v${VERSION}.sha256"
DIST_DIR="$SCRIPT_DIR/dist"
RELEASE_DIR="$DIST_DIR/v${VERSION}"

# ── Banner ────────────────────────────────────────────────────────
clear
echo -e "${CYN}${BLD}"
echo "  ReviGuard — Release Builder"
echo -e "${RST}"
echo -e "  ${BLD}Version:${RST}    v${VERSION}"
echo -e "  ${BLD}Archiv:${RST}     ${ARCHIVE_NAME}"
echo -e "  ${BLD}Zielordner:${RST} dist/v${VERSION}/"
echo
echo -e "  ${DIM}──────────────────────────────────────────────────────${RST}"
echo

# ── Git-Status prüfen ────────────────────────────────────────────
if git status --porcelain 2>/dev/null | grep -q '^[^?]'; then
  warn "Es gibt uncommittete Änderungen:"
  git status --short 2>/dev/null | head -10 | sed 's/^/    /'
  echo
  read -rp "  Trotzdem Release bauen? [j/N]: " CONFIRM
  [[ "$CONFIRM" =~ ^[JjYy]$ ]] || { echo "  Abgebrochen."; exit 0; }
  echo
fi

# ── Git-Tag setzen (optional) ─────────────────────────────────────
if git tag 2>/dev/null | grep -q "^v${VERSION}$"; then
  info "Git-Tag v${VERSION} existiert bereits."
else
  read -rp "  Git-Tag 'v${VERSION}' erstellen? [J/n]: " TAG_CONFIRM
  TAG_CONFIRM="${TAG_CONFIRM:-j}"
  if [[ "$TAG_CONFIRM" =~ ^[JjYy]$ ]]; then
    git tag -a "v${VERSION}" -m "Release v${VERSION}" 2>/dev/null
    ok "Git-Tag v${VERSION} erstellt."
    echo
    warn "Tag pushen mit: git push origin v${VERSION}"
    echo
  fi
fi

# ── Ausgabe-Verzeichnis vorbereiten ───────────────────────────────
mkdir -p "$RELEASE_DIR"

# Altes Archiv entfernen falls vorhanden
[[ -f "$RELEASE_DIR/$ARCHIVE_NAME"  ]] && rm -f "$RELEASE_DIR/$ARCHIVE_NAME"
[[ -f "$RELEASE_DIR/$CHECKSUM_NAME" ]] && rm -f "$RELEASE_DIR/$CHECKSUM_NAME"

# ── Archiv erstellen ──────────────────────────────────────────────
info "Erstelle Release-Archiv..."

# Dateien die NICHT ins Archiv sollen
# Hinweis: --exclude wird vor --transform angewendet, daher ./ als Prefix
EXCLUDES=(
  "./.git"
  "./.env"
  "./vendor"
  "./node_modules"
  "./dist"
  "./storage/logs"
  "./storage/framework/cache"
  "./storage/framework/sessions"
  "./storage/framework/views"
  "./bootstrap/cache"
  "./install.log"
  "./*.log"
  "./.DS_Store"
  "./Thumbs.db"
  # Entwickler-Dateien (nicht für Kunden)
  "./CLAUDE.md"
  "./DEVELOPER_DIARY.txt"
  "./DEVELOPER_DIARY.html"
  "./build-release.sh"
  "./git-push.txt"
  "./setup.sh"
  "./setup_laravel.sh"
  "./.claude"
  "./tests"
  "./*.png"
  "./*.jpg"
  "./*.jpeg"
)

EXCLUDE_ARGS=()
for excl in "${EXCLUDES[@]}"; do
  EXCLUDE_ARGS+=("--exclude=${excl}")
done

tar -czf "$RELEASE_DIR/$ARCHIVE_NAME" \
  "${EXCLUDE_ARGS[@]}" \
  --transform "s|^\.|reviguard|" \
  -C "$SCRIPT_DIR" \
  .

ARCHIVE_SIZE=$(du -sh "$RELEASE_DIR/$ARCHIVE_NAME" | cut -f1)
ok "Archiv erstellt: ${BLD}${ARCHIVE_NAME}${RST} (${ARCHIVE_SIZE})"

# ── Prüfsumme erstellen ───────────────────────────────────────────
info "Erstelle SHA256-Prüfsumme..."
cd "$RELEASE_DIR"
sha256sum "$ARCHIVE_NAME" > "$CHECKSUM_NAME"
CHECKSUM=$(cat "$CHECKSUM_NAME" | awk '{print $1}')
ok "Prüfsumme: ${DIM}${CHECKSUM}${RST}"
cd "$SCRIPT_DIR"

# ── latest.txt aktualisieren ──────────────────────────────────────
echo "$VERSION" > "$DIST_DIR/latest.txt"
ok "latest.txt aktualisiert → v${VERSION}"

# ── Inhalt prüfen ────────────────────────────────────────────────
echo
echo -e "  ${BLD}Archiv-Inhalt (Übersicht):${RST}"
tar -tzf "$RELEASE_DIR/$ARCHIVE_NAME" | head -20 | sed 's/^/    /'
TOTAL_FILES=$(tar -tzf "$RELEASE_DIR/$ARCHIVE_NAME" | wc -l)
echo -e "    ${DIM}... gesamt ${TOTAL_FILES} Dateien${RST}"

# ── Zusammenfassung ───────────────────────────────────────────────
echo
echo -e "${GRN}${BLD}"
echo "  ╔══════════════════════════════════════════════════════════════╗"
echo "  ║   Release v${VERSION} erfolgreich erstellt!                ║"
echo "  ╚══════════════════════════════════════════════════════════════╝"
echo -e "${RST}"
echo -e "  ${BLD}Erstellte Dateien:${RST}"
echo -e "  ${DIM}dist/v${VERSION}/${ARCHIVE_NAME}${RST}"
echo -e "  ${DIM}dist/v${VERSION}/${CHECKSUM_NAME}${RST}"
echo -e "  ${DIM}dist/latest.txt${RST}"
echo
echo -e "  ${BLD}Nächste Schritte — Upload auf Release-Server:${RST}"
echo
echo -e "  ${CYN}Option A: Eigener Server (scp)${RST}"
echo -e "  ${DIM}scp dist/v${VERSION}/${ARCHIVE_NAME}  user@releases.reviguard.de:/var/www/releases/v${VERSION}/${RST}"
echo -e "  ${DIM}scp dist/v${VERSION}/${CHECKSUM_NAME} user@releases.reviguard.de:/var/www/releases/v${VERSION}/${RST}"
echo -e "  ${DIM}scp dist/latest.txt                   user@releases.reviguard.de:/var/www/releases/${RST}"
echo
echo -e "  ${CYN}Option B: GitHub Releases (gh CLI)${RST}"
echo -e "  ${DIM}gh release create v${VERSION} \\${RST}"
echo -e "  ${DIM}  dist/v${VERSION}/${ARCHIVE_NAME} \\${RST}"
echo -e "  ${DIM}  dist/v${VERSION}/${CHECKSUM_NAME} \\${RST}"
echo -e "  ${DIM}  --title 'ReviGuard v${VERSION}' --notes ''${RST}"
echo
echo -e "  ${BLD}Kunden installieren dann mit:${RST}"
echo -e "  ${GRN}${BLD}  curl -fsSL https://get.reviguard.de/install.sh | bash${RST}"
echo
