<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function gala_dent_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support(
        'html5',
        [
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
            'search-form',
        ]
    );
}
add_action('after_setup_theme', 'gala_dent_theme_setup');

function gala_dent_enqueue_assets(): void
{
    $style_path = get_stylesheet_directory() . '/style.css';
    $version = file_exists($style_path) ? (string) filemtime($style_path) : null;

    wp_enqueue_style('gala-dent-theme', get_stylesheet_uri(), [], $version);
}
add_action('wp_enqueue_scripts', 'gala_dent_enqueue_assets');

function gala_dent_asset_url(string $relative_path): string
{
    return trailingslashit(get_stylesheet_directory_uri()) . ltrim($relative_path, '/');
}

function gala_dent_page_url(string $slug = ''): string
{
    $normalized_slug = trim($slug, '/');

    if ($normalized_slug === '' || $normalized_slug === 'index') {
        return home_url('/');
    }

    $page = get_page_by_path($normalized_slug);

    if ($page instanceof WP_Post) {
        return get_permalink($page);
    }

    return home_url('/' . $normalized_slug . '/');
}

function gala_dent_static_template_path(string $slug): string
{
    return get_stylesheet_directory() . '/static/pages/' . $slug . '.html';
}

function gala_dent_has_static_template(string $slug): bool
{
    return file_exists(gala_dent_static_template_path($slug));
}

function gala_dent_render_static_page(string $slug): void
{
    $template_path = gala_dent_static_template_path($slug);

    if (!file_exists($template_path) || !is_readable($template_path)) {
        status_header(404);
        gala_dent_render_fallback_page(__('Nie udało się odnaleźć szablonu tej strony.', 'gala-dent'));
        return;
    }

    $html = file_get_contents($template_path);

    if ($html === false) {
        status_header(500);
        gala_dent_render_fallback_page(__('Nie udało się wczytać zawartości strony.', 'gala-dent'));
        return;
    }

    $html = preg_replace('/<title>.*?<\/title>/is', '', $html, 1) ?? $html;

    $replacements = [
        '../index.html#/o-klinice' => gala_dent_page_url('o-klinice'),
        '../index.html#/oferta' => gala_dent_page_url('oferta'),
        '../index.html#/sprzet' => gala_dent_page_url('sprzet'),
        '../index.html#/kontakt' => gala_dent_page_url('kontakt'),
        '../index.html#/' => gala_dent_page_url(''),
        '../brand/' => trailingslashit(gala_dent_asset_url('assets/brand')),
        '../gallery/' => trailingslashit(gala_dent_asset_url('assets/gallery')),
        'target="_parent"' => '',
    ];

    $html = strtr($html, $replacements);

    $html = gala_dent_inject_body_classes($html, $slug);
    $html = gala_dent_inject_wp_head($html);
    $html = gala_dent_inject_wp_body_open($html);
    $html = gala_dent_inject_wp_footer($html);

    echo $html;
}

function gala_dent_inject_body_classes(string $html, string $slug): string
{
    $pattern = '/<body([^>]*)class="([^"]*)"([^>]*)>/i';

    if (!preg_match($pattern, $html, $matches)) {
        return $html;
    }

    $static_classes = preg_split('/\s+/', trim($matches[2])) ?: [];
    $extra_classes = [
        'gala-dent-theme',
        'gala-dent-page',
        'gala-dent-page-' . sanitize_html_class($slug),
    ];

    $body_classes = array_unique(
        array_filter(
            get_body_class(array_merge($static_classes, $extra_classes))
        )
    );

    $attributes = trim($matches[1] . ' ' . $matches[3]);
    $replacement = '<body';

    if ($attributes !== '') {
        $replacement .= ' ' . $attributes;
    }

    $replacement .= ' class="' . esc_attr(implode(' ', $body_classes)) . '">';

    return preg_replace($pattern, $replacement, $html, 1) ?? $html;
}

function gala_dent_inject_wp_head(string $html): string
{
    ob_start();
    wp_head();
    $wp_head = trim((string) ob_get_clean());

    if ($wp_head === '') {
        return $html;
    }

    return str_replace('</head>', $wp_head . PHP_EOL . '</head>', $html);
}

function gala_dent_inject_wp_body_open(string $html): string
{
    ob_start();
    wp_body_open();
    $wp_body_open = trim((string) ob_get_clean());

    if ($wp_body_open === '') {
        return $html;
    }

    return preg_replace('/(<body[^>]*>)/i', '$1' . PHP_EOL . $wp_body_open, $html, 1) ?? $html;
}

function gala_dent_inject_wp_footer(string $html): string
{
    ob_start();
    wp_footer();
    $wp_footer = trim((string) ob_get_clean());

    if ($wp_footer === '') {
        return $html;
    }

    return str_replace('</body>', $wp_footer . PHP_EOL . '</body>', $html);
}

function gala_dent_current_slug(): string
{
    $slug = '';

    if (is_page()) {
        $slug = get_post_field('post_name', get_queried_object_id());
    }

    return is_string($slug) ? $slug : '';
}

function gala_dent_render_fallback_page(string $message = ''): void
{
    $title = wp_get_document_title();
    $content_message = $message !== '' ? $message : __('Treść tej strony możesz uzupełnić bezpośrednio w WordPressie albo dodać kolejny statyczny szablon do motywu.', 'gala-dent');
    ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('gala-dent-theme gala-dent-fallback-page'); ?>>
<?php wp_body_open(); ?>
<main style="min-height:100vh;padding:96px 24px;background:#fbf9f3;color:#1b1c19;font-family:'Plus Jakarta Sans',sans-serif;">
    <div style="max-width:840px;margin:0 auto;">
        <p style="margin:0 0 12px;color:#6f5b3b;font-size:12px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;">GALA DENT</p>
        <h1 style="margin:0 0 20px;font-family:'Manrope',sans-serif;font-size:clamp(2rem,5vw,3.5rem);line-height:1.05;"><?php echo esc_html($title); ?></h1>
        <p style="max-width:680px;margin:0 0 32px;color:#4d463c;font-size:1.05rem;line-height:1.8;"><?php echo esc_html($content_message); ?></p>
        <p style="margin:0;">
            <a href="<?php echo esc_url(gala_dent_page_url('kontakt')); ?>" style="display:inline-flex;align-items:center;justify-content:center;padding:14px 24px;border-radius:8px;background:#6f5b3b;color:#fff;text-decoration:none;font-weight:700;">Skontaktuj się z nami</a>
        </p>
    </div>
</main>
<?php wp_footer(); ?>
</body>
</html>
    <?php
}

function gala_dent_missing_page_slugs(): array
{
    $required_slugs = ['o-klinice', 'oferta', 'sprzet', 'kontakt'];
    $missing = [];

    foreach ($required_slugs as $slug) {
        if (!(get_page_by_path($slug) instanceof WP_Post)) {
            $missing[] = $slug;
        }
    }

    return $missing;
}

function gala_dent_admin_setup_notice(): void
{
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    if ($screen && !in_array($screen->base, ['dashboard', 'themes', 'page'], true)) {
        return;
    }

    $missing_pages = gala_dent_missing_page_slugs();

    if ($missing_pages === []) {
        return;
    }
    ?>
    <div class="notice notice-warning">
        <p>
            <?php
            echo esc_html__(
                'Motyw GALA DENT działa najlepiej po utworzeniu stron o slugach:',
                'gala-dent'
            );
            ?>
            <strong><?php echo esc_html(implode(', ', $missing_pages)); ?></strong>.
        </p>
    </div>
    <?php
}
add_action('admin_notices', 'gala_dent_admin_setup_notice');
