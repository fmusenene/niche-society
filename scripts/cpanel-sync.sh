#!/usr/bin/env bash
# Sync production with GitHub — run from repo root on cPanel.
# Usage: bash scripts/cpanel-sync.sh
#
# Resets tracked files to match GitHub. Backs up and restores local config.

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

BRANCH="${DEPLOY_BRANCH:-main}"
REMOTE="${DEPLOY_REMOTE:-origin}"

CONFIG_FILES=(
  config/admin-settings.php
  config/secrets.local.php
  config/database.local.php
  config/config.php
  config/database.php
  config/email.php
)

echo "==> Niche Society — cPanel sync"
echo "    Directory: $ROOT"

if ! git rev-parse --git-dir >/dev/null 2>&1; then
  echo "ERROR: Not a git repository."
  exit 1
fi

TMP_BACKUP="$(mktemp -d)"
echo "==> Backing up local config..."
for f in "${CONFIG_FILES[@]}"; do
  if [[ -f "$f" ]]; then
    mkdir -p "$TMP_BACKUP/$(dirname "$f")"
    cp "$f" "$TMP_BACKUP/$f"
    echo "    kept $f"
  fi
done

echo "==> Fetching $REMOTE/$BRANCH..."
git fetch "$REMOTE" "$BRANCH"

echo "==> Resetting tracked files (fixes merge conflicts)..."
git reset --hard "$REMOTE/$BRANCH"

echo "==> Restoring local config..."
for f in "${CONFIG_FILES[@]}"; do
  if [[ -f "$TMP_BACKUP/$f" ]]; then
    mkdir -p "$(dirname "$f")"
    cp "$TMP_BACKUP/$f" "$f"
  fi
done
rm -rf "$TMP_BACKUP"

ensure_file() {
  local example="$1"
  local target="$2"
  if [[ ! -f "$target" && -f "$example" ]]; then
    cp "$example" "$target"
    echo "    Created $target from example — edit before use."
  fi
}

echo "==> Ensuring config examples..."
ensure_file config/admin-settings.php.example config/admin-settings.php
ensure_file config/secrets.local.php.example   config/secrets.local.php
ensure_file config/database.local.php.example  config/database.local.php
ensure_file config/config.example.php          config/config.php

mkdir -p logs assets/images/services
chmod 755 logs assets/images/services 2>/dev/null || true

echo "==> Sync complete. Site matches GitHub."
