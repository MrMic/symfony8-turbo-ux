# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Purpose

Learning/exploration project for **Symfony UX Turbo** (Hotwire Turbo). Features intentional demo patterns like `sleep(1)` in `PagesController::about()` to showcase Turbo's progress bar.

## Commands

```bash
# Dev server (required flags for preview to work)
symfony server:start --no-tls --allow-all-ip --port=8001

# Console
php bin/console <command>
# or
symfony console <command>

# Tests
php bin/phpunit
php bin/phpunit tests/path/to/SomeTest.php   # single test file

# Linting
vendor/bin/php-cs-fixer fix                  # auto-fix (PHP-CS-Fixer)
vendor/bin/phpcs                             # check PSR-12 compliance
vendor/bin/phpcbf                            # auto-fix PSR-12

# Assets (no build step — AssetMapper handles everything)
php bin/console importmap:install            # install JS packages from importmap.php
php bin/console assets:install               # symlink/copy assets to public/
php bin/console debug:asset-map              # list all mapped asset paths
```

## Architecture

**No Node.js/npm build pipeline.** Assets are served via Symfony AssetMapper + importmap (`importmap.php`). Adding a JS package: `php bin/console importmap:require package-name`.

### Turbo Stream pattern

The contact form (`MessagesController`) demonstrates the core Turbo Stream pattern:

1. Controller detects `TurboBundle::STREAM_FORMAT` preferred format (set automatically by Turbo on form submit)
2. Returns a `text/vnd.turbo-stream.html` response rendering a `.stream.html.twig` template
3. The stream template uses `<turbo-stream action="replace" target="...">` to update the DOM

Stream templates live alongside regular templates with `.stream.html.twig` suffix. The target DOM element must have a matching `id` attribute in the base template.

### Frontend stack

- **Turbo** (`@hotwired/turbo` 7.3): drives SPA-like navigation and form submissions
- **Stimulus** (`@hotwired/stimulus` 3.2): loaded via `@symfony/stimulus-bundle`, controllers in `assets/controllers/`
- **Bootstrap 5.3**: loaded via importmap, no custom build
- Entry point: `assets/app.js` → configures Turbo progress bar delay, imports Stimulus bootstrap and styles

### Directory conventions

| Path | Purpose |
|------|---------|
| `templates/partials/` | Reusable Twig partials (nav, flash messages) |
| `templates/*/success.stream.html.twig` | Turbo Stream response templates |
| `assets/controllers/` | Stimulus controllers |
| `config/packages/ux_turbo.yaml` | Enables stateless CSRF header check for Turbo |

### HTTP status codes

Forms return `422 Unprocessable Entity` on validation failure (required by Turbo to re-render the form instead of treating it as a success).
