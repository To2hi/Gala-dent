<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (is_front_page()) {
    gala_dent_render_static_page('index');
    return;
}

if (is_page()) {
    $slug = gala_dent_current_slug();

    if ($slug !== '' && gala_dent_has_static_template($slug)) {
        gala_dent_render_static_page($slug);
        return;
    }
}

gala_dent_render_fallback_page(__('Ta część witryny nie ma jeszcze dedykowanego szablonu w motywie.', 'gala-dent'));
