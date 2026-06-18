#!/bin/bash
# SessionStart hook for Claude Code on the web.
#
# This repo is a pure-PHP WordPress theme (wp-theme/seniormassan/) with no build
# step and no test suite. The only "linter" is `php -l` (syntax check), so this
# hook just guarantees the PHP toolchain is present and installs Composer
# dependencies if a manifest is ever added.
set -euo pipefail

# Only run in Claude Code on the web (remote) sessions.
if [ "${CLAUDE_CODE_REMOTE:-}" != "true" ]; then
  exit 0
fi

cd "${CLAUDE_PROJECT_DIR:-$(pwd)}"

# Run a command as root if needed (sudo only when not already root).
as_root() {
  if [ "$(id -u)" -eq 0 ]; then
    "$@"
  elif command -v sudo >/dev/null 2>&1; then
    sudo "$@"
  else
    "$@"
  fi
}

# Ensure the PHP CLI is available — `php -l` depends on it.
if ! command -v php >/dev/null 2>&1; then
  echo "PHP CLI not found — installing php-cli..."
  if command -v apt-get >/dev/null 2>&1; then
    as_root apt-get update -y
    as_root apt-get install -y --no-install-recommends php-cli
  else
    echo "ERROR: php is missing and no apt-get available to install it." >&2
    exit 1
  fi
fi

# Install Composer dependencies only if a manifest exists (none today, but this
# keeps the hook correct if one is added later). Idempotent.
if [ -f composer.json ]; then
  if command -v composer >/dev/null 2>&1; then
    echo "composer.json found — running composer install..."
    composer install --no-interaction --prefer-dist
  else
    echo "composer.json present but composer not installed; skipping." >&2
  fi
fi

php -v
echo "Session start hook complete: PHP toolchain ready (lint with: php -l <file>)."
