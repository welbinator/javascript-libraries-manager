# JavaScript Libraries Manager - AI Agent Instructions

## Architecture Overview

This WordPress plugin manages JavaScript library loading through a procedural, namespaced PHP architecture. No classes—only functions in the `JS_Libs_Manager` namespace.

**Core Data Flow:**
1. **Registry** (`config.php`) → defines available libraries with `label`, `enqueue_callback`, `file`
2. **Admin UI** (`admin.php`) → saves global settings to `js_libs_manager_enabled_libs` option
3. **Taxonomy** (`js_library`) → allows per-post/page library selection via editor sidebar
4. **Frontend** (`frontend.php`) → reads global settings + post terms, calls enqueue callbacks
5. **Library files** (`includes/libraries/*.php`) → each implements an enqueue function

**Critical Decision Logic** (in `enqueue_enabled_libraries()`):
- Global setting enabled → load site-wide (priority 1)
- Global disabled + post has taxonomy term → load only on that page (priority 2)
- This order is intentional—don't reverse it without understanding site-wide impact

## File Map

```
javascript-libraries-manager.php  # Entry: constants, taxonomy registration, includes
includes/
  config.php                      # $js_libs_manager_libraries array + term creation
  admin.php                       # Settings page, option sanitization
  frontend.php                    # Enqueue decision logic (wp_enqueue_scripts hook)
  libraries/
    swiper.php                    # Example: CSS + JS + window.Swiper = Swiper
    gsap.php                      # Example: multiple scripts with dependencies
    fontawesome.php               # Example: user-provided kit URL
github-update.php                 # GitHub Releases update checker
```

## Adding a New Library (Step-by-Step)

1. **Create library file** at `includes/libraries/mylibrary.php`:
```php
<?php
namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function js_libs_manager_enqueue_mylibrary() {
    wp_enqueue_script(
        'js-libs-manager-mylibrary',
        'https://cdn.example.com/mylibrary@1.0.0/dist/mylibrary.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );
    
    // CRITICAL: Use 'after' position to expose UMD globals after script loads
    wp_add_inline_script(
        'js-libs-manager-mylibrary',
        'window.MyLibrary = MyLibrary;',
        'after'  // ← Must be 'after', not 'before'
    );
}
```

2. **Register in `config.php`** (`$js_libs_manager_libraries` array):
```php
'mylibrary' => [
    'label'            => __( 'My Library', 'js-libs-manager' ),
    'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_mylibrary',
    'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/mylibrary.php',
],
```

3. **Test**: Taxonomy terms auto-create on `admin_init` via signature change detection. Visit Settings → JS Libraries to verify checkbox appears.

## Critical Patterns

### Taxonomy Term Generation
- Terms are created from `label` (not array key), e.g., `'GSAP (with ScrollTrigger)'` → slug `'gsap-with-scrolltrigger'`
- `frontend.php` matches post terms against `sanitize_title( $lib['label'] )`
- **Gotcha**: Changing a label breaks existing post associations—use `?recreate_terms=1` carefully

### Inline Script Position (`'after'` is critical)
Most libraries are UMD bundles that define globals at the end of execution. Using `'before'` will fail:
```php
// ✅ Correct - global exists after script runs
wp_add_inline_script( 'handle', 'window.Swiper = Swiper;', 'after' );

// ❌ Wrong - global doesn't exist yet
wp_add_inline_script( 'handle', 'window.Swiper = Swiper;', 'before' );
```

### Script Dependencies
See `gsap.php` for multi-script pattern:
```php
wp_enqueue_script( 'gsap-js', '...gsap.min.js', array(), ... );
wp_enqueue_script( 'gsap-st', '...ScrollTrigger.min.js', array('gsap-js'), ... );
// ↑ ScrollTrigger depends on GSAP core
```

### Font Awesome Special Case
- User provides kit URL via admin input field
- Stored in separate option: `js_libs_manager_fontawesome_kit`
- Enqueued in `<head>` (not footer) with `crossorigin="anonymous"` attribute
- See `fontawesome.php` and `admin.php` for full implementation

## Testing Workflows

**Manual Testing (no automated tests exist):**
1. Install in `wp-content/plugins/` and activate
2. Enable `WP_DEBUG` in `wp-config.php` to see `error_log()` messages
3. Test global loading: Settings → JS Libraries → check library → visit frontend → DevTools Console: type `window.LibraryName`
4. Test per-page: Edit post → JS Libraries panel → select term → visit post → verify library loaded only there
5. Verify globals exposed: all libraries should be accessible via `window.LibraryName` pattern

**Debug Term Issues:**
- Visit `wp-admin/options-general.php?recreate_terms=1` (safe, creates missing terms)
- Force delete and recreate: `?recreate_terms=1&force=1` (destructive, backs up first!)

## Naming Conventions

- **Functions**: `js_libs_manager_enqueue_<slug>()`
- **Script handles**: `js-libs-manager-<name>` (use hyphens)
- **Option names**: `js_libs_manager_<name>` (use underscores)
- **Taxonomy**: `js_library` (singular, for posts/pages)
- **Namespace**: `JS_Libs_Manager` (all new functions must use this)

## GitHub Update System

`github-update.php` hooks `pre_set_site_transient_update_plugins` to check for releases:
- Queries `https://api.github.com/repos/welbinator/javascript-libraries-manager/releases/latest`
- Compares `tag_name` (minus `v` prefix) against `JS_LIBS_MANAGER_VERSION`
- If newer, adds update to transient using first asset's `browser_download_url`
- **Fork maintainers**: Update `$owner` and `$repo` variables in `check_for_updates()`

## Common Pitfalls

1. **Using library array key as taxonomy slug** — Don't. Frontend uses `sanitize_title( $lib['label'] )` not the key.
2. **Forgetting namespace** — All functions must be in `JS_Libs_Manager` namespace or fully qualified.
3. **Hardcoded CDN versions** — Pinned for stability. Update deliberately and test on staging.
4. **Reversing enqueue priority** — Global settings must override per-page (see `frontend.php` logic).
5. **Manual term creation** — Don't. The `admin_init` hook auto-syncs on library changes via signature hash.

## Version Bumping

When releasing:
1. Update `Version:` in `javascript-libraries-manager.php` header
2. Update `JS_LIBS_MANAGER_VERSION` constant
3. Git tag with `v` prefix (e.g., `v1.2.0`) for GitHub update system
4. Push tag: `git push origin v1.2.0`

---

**Last Updated**: 2025-11-28 — Tell me which library you want to add and I'll generate the exact code.
