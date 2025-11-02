# JavaScript Libraries Manager

A WordPress plugin to centrally manage and selectively load popular JavaScript libraries (GSAP, Swiper, Font Awesome, etc.) either site-wide or on specific pages.

## Features

- **Centralized Management**: Enable/disable libraries globally from a single settings page.
- **Per-Page Loading**: Selectively load libraries only on pages that need them.
- **Popular Libraries Included**:
  - GSAP (with ScrollTrigger)
  - Swiper.js (with CSS)
  - Anime.js
  - Chart.js
  - Font Awesome (via your kit)
  - Popper.js (v2)
  - Floating UI (DOM)
  - Embla Carousel (with CSS)
  - Sortable.js
  - A11y Dialog

## Installation

1. Download the plugin from GitHub
2. Upload to your WordPress site's `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Visit Settings → JS Libraries to configure

## Usage

### Enable Libraries Site-Wide

1. Go to Settings → JS Libraries
2. Check the boxes next to libraries you want to load on every page
3. If enabling Font Awesome, paste your kit's script URL in the provided field
4. Save Changes

### Enable Libraries Per-Page

1. Edit any post or page
2. Find the "JS Libraries" box in the editor sidebar
3. Check the libraries you want to load on this specific page
4. Update/Publish the post

### Example: Using GSAP

First, enable GSAP either site-wide or on a specific page. Then in your theme or custom JS:

```javascript
// GSAP is available as a global
gsap.to(".my-element", {
    duration: 1,
    x: 100
});

// ScrollTrigger is also included
ScrollTrigger.create({
    trigger: ".my-trigger",
    animation: tween
});
```

## For Developers

### Adding a New Library

1. Add the library definition in `includes/config.php`:
```php
'my-lib' => [
    'label'            => __( 'My Library', 'js-libs-manager' ),
    'enqueue_callback' => __NAMESPACE__ . '\\js_libs_manager_enqueue_mylib',
    'file'             => JS_LIBS_MANAGER_PLUGIN_PATH . 'includes/libraries/mylib.php',
],
```

2. Create `includes/libraries/mylib.php`:
```php
<?php
namespace JS_Libs_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function js_libs_manager_enqueue_mylib() {
    wp_enqueue_script(
        'js-libs-manager-mylib',
        'https://cdn.example.com/mylib.min.js',
        array(),
        JS_LIBS_MANAGER_VERSION,
        true
    );
}
```

The library will automatically appear in the admin UI and be available for site-wide or per-page enabling.

### Library Files Pattern

- Use CDN URLs with pinned versions to avoid breaking changes
- Load scripts in footer by default (`true` as last param to `wp_enqueue_script`)
- For UMD builds that need global exposure, use:
```php
wp_add_inline_script( 'handle', 'window.MyLib = MyLib;', 'after' );
```

## Advanced Usage

### Recreating Taxonomy Terms

If you add a new library and it's not appearing in the editor:

1. Visit any admin page with `?recreate_terms=1` appended to the URL
2. This safely creates any missing terms without affecting existing selections

To force recreation (destructive, will remove existing selections):

1. Append `?recreate_terms=1&force=1` to any admin URL
2. Back up your database first!

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the GPL v2 or later - see the LICENSE file for details.