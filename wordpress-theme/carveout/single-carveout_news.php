<?php
if (!defined('ABSPATH')) {
    exit;
}

wp_safe_redirect(home_url('/news-detail/?id=' . get_the_ID()), 302);
exit;
