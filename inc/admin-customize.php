<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', function() {
    remove_meta_box('industrydiv', 'tax_service', 'side');
    remove_meta_box('challengediv', 'tax_service', 'side');
    remove_meta_box('areadiv', 'tax_service', 'side');
});

add_action('admin_head', function() {
    global $post_type;
    if ($post_type === 'tax_service') {
        echo '<style>
            .acf-tab-group { border-bottom: 2px solid #0066cc; background: #f5f5f5; }
            .acf-tab-button { padding: 12px 20px; font-weight: 500; }
            .acf-tab-button.active { background: #0066cc; color: #fff; }
            .acf-taxonomy-field .categorychecklist { max-height: 300px; overflow-y: auto; }
        </style>';
    }
});
