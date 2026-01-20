<?php
if (!defined('ABSPATH')) exit;

// サイドバーのタクソノミーメタボックスを削除
add_action('admin_menu', function() {
    remove_meta_box('industrydiv', 'tax_service', 'side');
    remove_meta_box('challengediv', 'tax_service', 'side');
    remove_meta_box('areadiv', 'tax_service', 'side');
});

// タクソノミーメタボックスをエディター内（normal位置）に再追加
add_action('add_meta_boxes', function() {
    add_meta_box('industry_meta_box', '対応業種', 'post_categories_meta_box', 'tax_service', 'normal', 'high', array('taxonomy' => 'industry'));
    add_meta_box('challenge_meta_box', '対応課題', 'post_categories_meta_box', 'tax_service', 'normal', 'high', array('taxonomy' => 'challenge'));
    add_meta_box('area_meta_box', '対応エリア', 'post_categories_meta_box', 'tax_service', 'normal', 'high', array('taxonomy' => 'area'));
});

// スタイル調整
add_action('admin_head', function() {
    global $post_type;
    if ($post_type === 'tax_service') {
        echo '<style>
            #industry_meta_box, #challenge_meta_box, #area_meta_box {
                border-left: 4px solid #0066cc;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            #industry_meta_box .hndle, #challenge_meta_box .hndle, #area_meta_box .hndle {
                background: #f8f9fa;
                font-weight: 600;
                color: #0066cc;
            }
            #industry_meta_box .inside, #challenge_meta_box .inside, #area_meta_box .inside {
                max-height: 300px;
                overflow-y: auto;
            }
        </style>';
    }
});
