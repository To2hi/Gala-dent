<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

status_header(404);
gala_dent_render_fallback_page(__('Nie znaleziono strony, której szukasz.', 'gala-dent'));
