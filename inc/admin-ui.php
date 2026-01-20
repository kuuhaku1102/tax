<?php
if (!defined('ABSPATH')) exit;

// 掲載制御メタボックスをサイドバーから削除してエディター内に移動
add_action('do_meta_boxes', function() {
    remove_meta_box('listing_control_meta', 'tax_service', 'side');
    add_meta_box('listing_control_meta', '掲載制御', 'display_listing_control_meta_box', 'tax_service', 'normal', 'high');
});

// 業種・課題・エリアのタクソノミーメタボックスをエディター内に移動
add_action('admin_menu', function() {
    remove_meta_box('service_industrydiv', 'tax_service', 'side');
    remove_meta_box('service_issuediv', 'tax_service', 'side');
    remove_meta_box('service_areadiv', 'tax_service', 'side');
});

add_action('add_meta_boxes', function() {
    add_meta_box('service_industry_meta', '対応業種', 'post_categories_meta_box', 'tax_service', 'normal', 'default', array('taxonomy' => 'service_industry'));
    add_meta_box('service_issue_meta', '対応課題', 'post_categories_meta_box', 'tax_service', 'normal', 'default', array('taxonomy' => 'service_issue'));
    add_meta_box('service_area_meta', '対応エリア', 'post_categories_meta_box', 'tax_service', 'normal', 'default', array('taxonomy' => 'service_area'));
});

// 管理画面のスタイル調整
add_action('admin_head', function() {
    global $post_type;
    if ($post_type === 'tax_service') {
        echo '<style>
            /* メタボックスのスタイル */
            #listing_control_meta,
            #service_industry_meta,
            #service_issue_meta,
            #service_area_meta {
                border-left: 4px solid #0066cc;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                margin-bottom: 20px;
            }
            
            #listing_control_meta .hndle,
            #service_industry_meta .hndle,
            #service_issue_meta .hndle,
            #service_area_meta .hndle {
                background: #f8f9fa;
                font-weight: 600;
                color: #0066cc;
                padding: 12px 15px;
            }
            
            #listing_control_meta .inside,
            #service_industry_meta .inside,
            #service_issue_meta .inside,
            #service_area_meta .inside {
                padding: 15px;
            }
            
            #service_industry_meta .inside,
            #service_issue_meta .inside,
            #service_area_meta .inside {
                max-height: 300px;
                overflow-y: auto;
            }
            
            /* 掲載制御のフィールドスタイル */
            #listing_control_meta table {
                width: 100%;
                border-collapse: collapse;
            }
            
            #listing_control_meta th {
                text-align: left;
                padding: 10px;
                background: #f5f5f5;
                font-weight: 600;
                width: 150px;
            }
            
            #listing_control_meta td {
                padding: 10px;
            }
            
            #listing_control_meta input[type="text"],
            #listing_control_meta input[type="date"],
            #listing_control_meta select {
                width: 100%;
                max-width: 400px;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            
            #listing_control_meta input[type="checkbox"] {
                margin-right: 8px;
            }
            
            #listing_control_meta .description {
                color: #666;
                font-size: 13px;
                margin-top: 5px;
            }
        </style>';
    }
});
