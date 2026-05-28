<?php
/**
 * CARVEOUT theme setup.
 */

if (!defined('ABSPATH')) {
    exit;
}

function carveout_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
}
add_action('after_setup_theme', 'carveout_theme_setup');

function carveout_theme_asset(string $path): string
{
    return esc_url(get_template_directory_uri() . '/' . ltrim($path, '/'));
}

function carveout_theme_page_url(string $slug = ''): string
{
    $slug = trim($slug, '/');
    if ($slug === '' || $slug === 'index') {
        return esc_url(home_url('/'));
    }
    return esc_url(home_url('/' . $slug . '/'));
}

function carveout_theme_print_wp_base(): void
{
    ?>
    <script>
      window.carveoutWpBaseUrl = <?php echo wp_json_encode(home_url('/')); ?>;
      window.carveoutThemeUrl = <?php echo wp_json_encode(get_template_directory_uri() . '/'); ?>;
    </script>
    <?php
}
add_action('wp_head', 'carveout_theme_print_wp_base', 1);

function carveout_theme_disable_admin_bar_on_frontend(): void
{
    if (!is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'carveout_theme_disable_admin_bar_on_frontend');

