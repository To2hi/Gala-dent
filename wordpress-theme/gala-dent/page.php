<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$slug = gala_dent_current_slug();

if ($slug !== '' && gala_dent_has_static_template($slug)) {
    gala_dent_render_static_page($slug);
    return;
}

gala_dent_render_fallback_page();
