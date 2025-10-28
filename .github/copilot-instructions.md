Project: JavaScript Libraries Manager (WordPress plugin)

Quick orientation
- Main entry: `javascript-libraries-manager.php` — defines constants, registers `js_library` taxonomy and includes the files under `includes/`.
- Config & registry: `includes/config.php` holds the library registry ($js_libs_manager_libraries). Each library has a `label`, `enqueue_callback` and `file` path.
- Admin UI: `includes/admin.php` exposes a settings page and stores enabled libs in the `js_libs_manager_enabled_libs` option.
- Frontend: `includes/frontend.php` decides which libraries to enqueue (global settings first, per-page taxonomy second) and calls the library enqueue callbacks.
- Library implementations: `includes/libraries/*.php` — each file registers scripts/styles with `wp_enqueue_script`/`wp_enqueue_style` and commonly uses `wp_add_inline_script(..., 'after')` to expose globals (e.g. `window.Swiper = Swiper;`).
- Update helper: `github-update.php` hooks `pre_set_site_transient_update_plugins` and queries GitHub Releases — update `$owner`/`$repo` if you fork.

Why the structure matters
- The project uses a small, procedural, namespaced architecture (namespace `JS_Libs_Manager`). There are no classes — contributors should add namespaced functions.
- `config.php` is the single source of truth for which libraries exist. To add a library, register it in the array there and create the file referenced by `file`.
- The frontend decision flow is intentional: global settings override per-page taxonomy selection. Keep that order when changing logic.

Conventions & patterns to follow
- Namespacing: use `JS_Libs_Manager\\function_name` or declare `namespace JS_Libs_Manager;` at top of files.
- Function names: `js_libs_manager_enqueue_<slug>()` for enqueue callbacks. The registry expects `enqueue_callback` to be callable.
- Handles & constants: use plugin-prefixed handles like `js-libs-manager-<name>` and `JS_LIBS_MANAGER_VERSION` for script versions.
- Inline scripts: many libraries rely on `wp_add_inline_script(..., 'after')` to expose UMD globals — preserve the 'after' placement unless you fully understand the module's build.
- Files: add library code in `includes/libraries/` and reference it from `includes/config.php`.

Developer workflows & tests (manual)
- No build step in repo — this is a PHP WordPress plugin.
- To test locally: copy the plugin folder into a WP install `wp-content/plugins/`, activate the plugin, visit Settings → "JS Libraries" to toggle global libs, and open a post/page and add the `js_library` term to test per-page loading.
- Debugging: enable `WP_DEBUG` to surface errors and check `error_log` (some functions log on WP_DEBUG). Use browser devtools to confirm scripts/styles loaded and globals (e.g., `window.Swiper`).
- Update check: `github-update.php` calls the GitHub API; if you host elsewhere, change `$owner` and `$repo`.

Integration points & gotchas
- Taxonomy slug generation: `create_library_taxonomy_terms()` uses the human-readable `label` to create term slugs via `sanitize_title()` — avoid expecting library keys (like `gsap`) to be the term slug.
- Per-page checks in `frontend.php` compare sanitized label slugs against post terms — if you change labels, existing terms may mismatch; use the optional term re-sync logic in `config.php` when changing labels.
- CDN versions are hardcoded in library files. Update with care and pin versions to avoid breaking changes.

Examples (where to change things)
- Add a new lib: edit `includes/config.php` (add entry) + create `includes/libraries/<name>.php` with a `js_libs_manager_enqueue_<name>()` function that calls `wp_enqueue_script` and (if needed) `wp_add_inline_script(..., 'after')`.
- Expose a global: in a library file, after enqueuing a UMD script do `wp_add_inline_script( 'handle', 'window.MyLib = MyLib;', 'after' );`
- Change update repo: edit `github-update.php` and replace `$owner` and `$repo`.

What NOT to change lightly
- The enqueue decision order in `includes/frontend.php` (global first, then per-page). Changing this will alter site-wide behavior.
- The taxonomy term creation logic if you rely on existing term slugs in content; prefer re-sync with `get_option('js_libs_manager_terms_synced')` guarded code.

If anything is ambiguous or you'd like sample PRs (e.g., adding a new library), tell me which library to add and I'll produce the minimal code changes.

Last updated: auto-generated — please review and tell me any missing examples or workflows you want included.
