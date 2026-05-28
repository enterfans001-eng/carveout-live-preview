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

function carveout_theme_register_content_types(): void
{
    $post_types = [
        'carveout_news' => [
            'name' => 'ニュース',
            'singular_name' => 'ニュース',
            'menu_icon' => 'dashicons-megaphone',
            'rewrite' => 'cms-news',
        ],
        'carveout_event' => [
            'name' => '事務所イベント',
            'singular_name' => '事務所イベント',
            'menu_icon' => 'dashicons-calendar-alt',
            'rewrite' => 'cms-events',
        ],
        'carveout_interview' => [
            'name' => 'インタビュー',
            'singular_name' => 'インタビュー',
            'menu_icon' => 'dashicons-format-chat',
            'rewrite' => 'cms-interview',
        ],
        'carveout_liver' => [
            'name' => '所属ライバー',
            'singular_name' => '所属ライバー',
            'menu_icon' => 'dashicons-groups',
            'rewrite' => 'cms-livers',
        ],
        'carveout_ranking' => [
            'name' => 'ランキング',
            'singular_name' => 'ランキング',
            'menu_icon' => 'dashicons-awards',
            'rewrite' => 'cms-ranking',
        ],
    ];

    foreach ($post_types as $post_type => $config) {
        register_post_type($post_type, [
            'labels' => [
                'name' => $config['name'],
                'singular_name' => $config['singular_name'],
                'add_new_item' => $config['singular_name'] . 'を追加',
                'edit_item' => $config['singular_name'] . 'を編集',
                'new_item' => '新規' . $config['singular_name'],
                'view_item' => $config['singular_name'] . 'を見る',
                'search_items' => $config['singular_name'] . 'を検索',
            ],
            'public' => true,
            'show_in_rest' => true,
            'menu_icon' => $config['menu_icon'],
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
            'has_archive' => false,
            'rewrite' => ['slug' => $config['rewrite']],
        ]);
    }
}
add_action('init', 'carveout_theme_register_content_types');

function carveout_theme_register_liver_taxonomy(): void
{
    register_taxonomy('carveout_liver_app', ['carveout_liver'], [
        'labels' => [
            'name' => '配信アプリ',
            'singular_name' => '配信アプリ',
            'add_new_item' => '配信アプリを追加',
        ],
        'public' => true,
        'show_in_rest' => true,
        'hierarchical' => false,
        'rewrite' => ['slug' => 'liver-app'],
    ]);
}
add_action('init', 'carveout_theme_register_liver_taxonomy');

function carveout_theme_meta_fields(): array
{
    return [
        'carveout_news' => [
            'carveout_source_url' => ['label' => '元記事URL（任意）', 'type' => 'url'],
            'carveout_image_url' => ['label' => '画像URL（アイキャッチ未設定時）', 'type' => 'url'],
        ],
        'carveout_event' => [
            'carveout_source_url' => ['label' => '元記事URL（任意）', 'type' => 'url'],
            'carveout_image_url' => ['label' => '画像URL（アイキャッチ未設定時）', 'type' => 'url'],
        ],
        'carveout_interview' => [
            'carveout_image_url' => ['label' => '画像URL（アイキャッチ未設定時）', 'type' => 'url'],
        ],
        'carveout_liver' => [
            'carveout_liver_app_text' => ['label' => '配信アプリ名（例：17LIVE / BIGOLIVE / TikTokLIVE / Pococha）', 'type' => 'text'],
            'carveout_profile_url' => ['label' => '配信アカウントURL', 'type' => 'url'],
            'carveout_instagram_url' => ['label' => 'Instagram URL（任意）', 'type' => 'url'],
            'carveout_image_url' => ['label' => '画像URL（アイキャッチ未設定時）', 'type' => 'url'],
        ],
        'carveout_ranking' => [
            'carveout_liver_app_text' => ['label' => '配信アプリ名（17LIVE / BIGOLIVE）', 'type' => 'text'],
            'carveout_ranking_type' => ['label' => 'ランキング種別（総合 / 新人）', 'type' => 'text'],
            'carveout_rank_number' => ['label' => '順位（1〜5）', 'type' => 'number'],
            'carveout_profile_url' => ['label' => '配信アカウントURL', 'type' => 'url'],
            'carveout_image_url' => ['label' => '画像URL（アイキャッチ未設定時）', 'type' => 'url'],
        ],
    ];
}

function carveout_theme_add_meta_boxes(): void
{
    foreach (carveout_theme_meta_fields() as $post_type => $fields) {
        add_meta_box(
            'carveout_content_settings',
            'CARVEOUT表示設定',
            'carveout_theme_render_meta_box',
            $post_type,
            'normal',
            'high',
            ['fields' => $fields]
        );
    }
}
add_action('add_meta_boxes', 'carveout_theme_add_meta_boxes');

function carveout_theme_render_meta_box(WP_Post $post, array $box): void
{
    wp_nonce_field('carveout_theme_save_meta', 'carveout_theme_meta_nonce');
    $fields = $box['args']['fields'] ?? [];

    echo '<table class="form-table"><tbody>';
    foreach ($fields as $key => $field) {
        $value = get_post_meta($post->ID, $key, true);
        $type = $field['type'] ?? 'text';
        printf(
            '<tr><th><label for="%1$s">%2$s</label></th><td><input class="regular-text" id="%1$s" name="%1$s" type="%3$s" value="%4$s"></td></tr>',
            esc_attr($key),
            esc_html($field['label']),
            esc_attr($type),
            esc_attr($value)
        );
    }
    echo '</tbody></table>';
    echo '<p>画像は「アイキャッチ画像」を優先します。URL欄は外部画像をそのまま使いたい場合に入力してください。</p>';
}

function carveout_theme_save_meta(int $post_id): void
{
    if (
        !isset($_POST['carveout_theme_meta_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['carveout_theme_meta_nonce'])), 'carveout_theme_save_meta')
        || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        || !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    $post_type = get_post_type($post_id);
    $fields = carveout_theme_meta_fields()[$post_type] ?? [];

    foreach ($fields as $key => $field) {
        if (!isset($_POST[$key])) {
            delete_post_meta($post_id, $key);
            continue;
        }

        $value = wp_unslash($_POST[$key]);
        if (($field['type'] ?? 'text') === 'url') {
            $value = esc_url_raw($value);
        } elseif (($field['type'] ?? 'text') === 'number') {
            $value = (string) absint($value);
        } else {
            $value = sanitize_text_field($value);
        }

        update_post_meta($post_id, $key, $value);
    }
}
add_action('save_post', 'carveout_theme_save_meta');

function carveout_theme_post_image_url(int $post_id): string
{
    $featured = get_the_post_thumbnail_url($post_id, 'large');
    if ($featured) {
        return esc_url_raw($featured);
    }

    return esc_url_raw((string) get_post_meta($post_id, 'carveout_image_url', true));
}

function carveout_theme_format_date(int $post_id): array
{
    $timestamp = get_post_time('U', false, $post_id);

    return [
        'date' => wp_date('Y.m.d', $timestamp),
        'datetime' => wp_date('Y-m-d', $timestamp),
    ];
}

function carveout_theme_get_cms_posts(string $post_type, int $limit = -1): array
{
    $query = new WP_Query([
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => ['date' => 'DESC', 'menu_order' => 'ASC'],
        'no_found_rows' => true,
    ]);

    $items = [];

    foreach ($query->posts as $post) {
        $date = carveout_theme_format_date($post->ID);
        $source_url = (string) get_post_meta($post->ID, 'carveout_source_url', true);
        $detail_slug = $post_type === 'carveout_interview' ? 'interview-detail' : 'news-detail';

        $items[] = [
            'id' => (string) $post->ID,
            'date' => $date['date'],
            'datetime' => $date['datetime'],
            'title' => get_the_title($post),
            'url' => $source_url ?: get_permalink($post),
            'detailUrl' => home_url('/' . $detail_slug . '/?id=' . $post->ID),
            'image' => carveout_theme_post_image_url($post->ID),
            'body' => array_values(array_filter([wp_strip_all_tags(get_the_excerpt($post))])),
            'detailHtml' => apply_filters('the_content', $post->post_content),
        ];
    }

    wp_reset_postdata();

    return $items;
}

function carveout_theme_get_livers(): array
{
    $query = new WP_Query([
        'post_type' => 'carveout_liver',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
        'no_found_rows' => true,
    ]);

    $items = [];

    foreach ($query->posts as $post) {
        $terms = wp_get_post_terms($post->ID, 'carveout_liver_app', ['fields' => 'names']);
        $category = (string) get_post_meta($post->ID, 'carveout_liver_app_text', true);
        if (!$category && !is_wp_error($terms) && !empty($terms)) {
            $category = $terms[0];
        }

        $items[] = [
            'name' => get_the_title($post),
            'category' => $category ?: '17LIVE',
            'url' => (string) get_post_meta($post->ID, 'carveout_profile_url', true),
            'instagramUrl' => (string) get_post_meta($post->ID, 'carveout_instagram_url', true),
            'image' => carveout_theme_post_image_url($post->ID),
        ];
    }

    wp_reset_postdata();

    return $items;
}

function carveout_theme_get_rankings(): array
{
    $query = new WP_Query([
        'post_type' => 'carveout_ranking',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'meta_value_num' => 'ASC'],
        'meta_key' => 'carveout_rank_number',
        'no_found_rows' => true,
    ]);

    $items = [];

    foreach ($query->posts as $post) {
        $items[] = [
            'name' => get_the_title($post),
            'category' => (string) get_post_meta($post->ID, 'carveout_liver_app_text', true),
            'type' => (string) get_post_meta($post->ID, 'carveout_ranking_type', true),
            'rank' => (int) get_post_meta($post->ID, 'carveout_rank_number', true),
            'url' => (string) get_post_meta($post->ID, 'carveout_profile_url', true),
            'image' => carveout_theme_post_image_url($post->ID),
        ];
    }

    wp_reset_postdata();

    return $items;
}

function carveout_theme_print_cms_data(): void
{
    if (is_admin()) {
        return;
    }

    $data = [
        'news' => carveout_theme_get_cms_posts('carveout_news'),
        'events' => carveout_theme_get_cms_posts('carveout_event'),
        'interviews' => carveout_theme_get_cms_posts('carveout_interview'),
        'livers' => carveout_theme_get_livers(),
        'rankings' => carveout_theme_get_rankings(),
    ];
    ?>
    <script>
      window.carveoutWpCms = <?php echo wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    </script>
    <?php
}
add_action('wp_head', 'carveout_theme_print_cms_data', 20);
