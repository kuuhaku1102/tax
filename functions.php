<?php
/**
 * Tax Matching Theme - Functions
 * 
 * 設計思想:
 * - 管理しやすさ最優先
 * - データとロジックの分離
 * - 拡張性の確保
 * 
 * @package TaxMatchingTheme
 * @version 1.0.0
 */

// ========================================
// ACF JSON同期の設定
// ========================================

/**
 * ACF JSONの保存先を設定
 * 
 * 設計意図:
 * - フィールドグループをJSONで管理
 * - バージョン管理が可能
 * - 環境間での同期が容易
 */
add_filter('acf/settings/save_json', function($path) {
    return get_stylesheet_directory() . '/acf-json';
});

/**
 * ACF JSONの読み込み先を設定
 */
add_filter('acf/settings/load_json', function($paths) {
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
});

// ========================================
// カスタム投稿タイプ登録
// ========================================

/**
 * tax_service カスタム投稿タイプを登録
 * 
 * 設計意図:
 * - 税理士「個人」ではなく「サービス単位」で管理
 * - 1税理士が複数サービスを提供する場合に対応
 * - 将来的な課金モデル変更に耐える構造
 */
add_action('init', function () {
    register_post_type('tax_service', [
        'label' => '税理士サービス',
        'labels' => [
            'name' => '税理士サービス',
            'singular_name' => '税理士サービス',
            'add_new' => '新規追加',
            'add_new_item' => '新しい税理士サービスを追加',
            'edit_item' => '税理士サービスを編集',
            'new_item' => '新しい税理士サービス',
            'view_item' => '税理士サービスを表示',
            'search_items' => '税理士サービスを検索',
            'not_found' => '税理士サービスが見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱に税理士サービスはありません',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'services'],
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-businessperson',
        'show_in_rest' => true, // Gutenberg対応
        'menu_position' => 5,
    ]);
});

// ========================================
// タクソノミー登録
// ========================================

/**
 * タクソノミーを一括登録
 * 
 * 設計意図:
 * - 変更頻度が低いものは専用タクソノミー
 * - 変更頻度が高いものは service_tag に集約
 * - 階層構造が必要なものは hierarchical => true
 */
add_action('init', function () {
    $taxonomies = [
        // 業種（変更頻度: 低、階層: あり）
        'service_industry' => [
            'label' => '業種',
            'hierarchical' => true,
            'show_admin_column' => true,
        ],
        // 課題・目的（変更頻度: 低、階層: あり）
        'service_issue' => [
            'label' => '課題・目的',
            'hierarchical' => true,
            'show_admin_column' => true,
        ],
        // フェーズ（変更頻度: 中、階層: なし）
        'service_phase' => [
            'label' => 'フェーズ',
            'hierarchical' => false,
            'show_admin_column' => true,
        ],
        // 対応エリア（変更頻度: 低、階層: あり）
        'service_area' => [
            'label' => '対応エリア',
            'hierarchical' => true,
            'show_admin_column' => true,
        ],
        // 補助タグ（変更頻度: 高、階層: なし）
        'service_tag' => [
            'label' => 'タグ',
            'hierarchical' => false,
            'show_admin_column' => false,
        ],
    ];

    foreach ($taxonomies as $slug => $args) {
        $default_args = [
            'public' => true,
            'rewrite' => ['slug' => $slug],
            'show_in_rest' => true, // Gutenberg対応
        ];
        
        register_taxonomy($slug, 'tax_service', array_merge($default_args, $args));
    }
});

// ========================================
// ヘルパー関数の読み込み
// ========================================

/**
 * ヘルパー関数ファイルを読み込み
 * 
 * 設計意図:
 * - functions.php を肥大化させない
 * - 役割ごとにファイルを分割
 */
$includes = [
    'inc/helpers.php',      // 汎用ヘルパー関数
    'inc/query.php',        // クエリ関連関数
    'inc/display.php',      // 表示関連関数
    'inc/admin.php',        // 管理画面カスタマイズ
];

foreach ($includes as $file) {
    $filepath = get_template_directory() . '/' . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

// ========================================
// テーマサポート
// ========================================

/**
 * テーマサポートを追加
 */
add_action('after_setup_theme', function () {
    // アイキャッチ画像のサポート
    add_theme_support('post-thumbnails');
    
    // カスタムロゴのサポート
    add_theme_support('custom-logo');
    
    // タイトルタグのサポート
    add_theme_support('title-tag');
    
    // HTML5サポート
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ]);
});

// ========================================
// アイキャッチ画像サイズの定義
// ========================================

/**
 * カスタム画像サイズを追加
 * 
 * 設計意図:
 * - 一覧ページ用のサムネイルサイズを統一
 * - パフォーマンス向上のため適切なサイズを定義
 */
add_action('after_setup_theme', function () {
    // サービスカード用（一覧ページ）
    add_image_size('service-card', 400, 300, true);
    
    // サービス詳細用（詳細ページ）
    add_image_size('service-detail', 800, 600, true);
});
