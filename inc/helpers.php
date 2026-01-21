<?php
/**
 * Helper Functions
 * 
 * 汎用的なヘルパー関数を定義
 * 
 * @package TaxMatchingTheme
 */

// ========================================
// 掲載プラン関連
// ========================================

/**
 * 掲載プラン名を優先度スコアに変換
 * 
 * 設計意図:
 * - プラン名（文字列）を数値化して並び替えに使用
 * - 将来的なプラン追加に対応可能
 * 
 * @param string $plan プラン名
 * @return int 優先度スコア
 */
function get_listing_plan_score($plan) {
    $scores = [
        'premium' => 100,
        'standard' => 50,
        'basic' => 10,
    ];
    
    $plan_lower = strtolower($plan);
    return isset($scores[$plan_lower]) ? $scores[$plan_lower] : 0;
}

/**
 * 掲載プランのラベルを取得
 * 
 * @param string $plan プラン名
 * @return string 表示用ラベル
 */
function get_listing_plan_label($plan) {
    $labels = [
        'premium' => 'プレミアム',
        'standard' => 'スタンダード',
        'basic' => 'ベーシック',
    ];
    
    $plan_lower = strtolower($plan);
    return isset($labels[$plan_lower]) ? $labels[$plan_lower] : $plan;
}

// ========================================
// 掲載ステータス関連
// ========================================

/**
 * サービスが掲載中かどうかをチェック
 * 
 * 設計意図:
 * - 掲載ステータス、掲載期間を総合的に判定
 * - テンプレート側で複雑な条件分岐をさせない
 * 
 * @param int $post_id 投稿ID
 * @return bool 掲載中ならtrue
 */
function is_service_active($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // 掲載ステータスのチェック
    $listing_status = get_field('listing_status', $post_id);
    if (!$listing_status) {
        return false;
    }
    
    // 掲載期間のチェック
    $start_date = get_field('listing_start_date', $post_id);
    $end_date = get_field('listing_end_date', $post_id);
    $today = date('Y-m-d');
    
    // 開始日チェック
    if ($start_date && $start_date > $today) {
        return false;
    }
    
    // 終了日チェック（空なら無期限）
    if ($end_date && $end_date < $today) {
        return false;
    }
    
    return true;
}

// ========================================
// ACFフィールド取得関連
// ========================================

/**
 * ACFフィールドを安全に取得
 * 
 * 設計意図:
 * - 空の場合にデフォルト値を返す
 * - テンプレート側での存在チェックを簡略化
 * 
 * @param string $field_name フィールド名
 * @param int $post_id 投稿ID
 * @param mixed $default デフォルト値
 * @return mixed フィールドの値
 */
function get_field_safe($field_name, $post_id = null, $default = '') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $value = get_field($field_name, $post_id);
    return $value ? $value : $default;
}

/**
 * ACFフィールドを安全に表示
 * 
 * @param string $field_name フィールド名
 * @param int $post_id 投稿ID
 * @param string $before 前に付ける文字列
 * @param string $after 後に付ける文字列
 */
function the_field_safe($field_name, $post_id = null, $before = '', $after = '') {
    $value = get_field_safe($field_name, $post_id);
    if ($value) {
        echo $before . wp_kses_post($value) . $after;
    }
}

// ========================================
// タクソノミー関連
// ========================================

/**
 * タクソノミータームを取得して表示
 * 
 * 設計意図:
 * - タームの存在チェックと表示を一括処理
 * - テンプレート側のコードを簡潔にする
 * 
 * @param string $taxonomy タクソノミー名
 * @param int $post_id 投稿ID
 * @param string $separator 区切り文字
 * @return string タームのリスト
 */
function get_taxonomy_terms_list($taxonomy, $post_id = null, $separator = ', ') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $terms = get_the_terms($post_id, $taxonomy);
    if (!$terms || is_wp_error($terms)) {
        return '';
    }
    
    $term_names = array_map(function($term) {
        return esc_html($term->name);
    }, $terms);
    
    return implode($separator, $term_names);
}

/**
 * タクソノミータームをリンク付きで表示
 * 
 * @param string $taxonomy タクソノミー名
 * @param int $post_id 投稿ID
 * @param string $separator 区切り文字
 */
function the_taxonomy_terms_links($taxonomy, $post_id = null, $separator = ', ') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $terms = get_the_terms($post_id, $taxonomy);
    if (!$terms || is_wp_error($terms)) {
        return;
    }
    
    $links = [];
    foreach ($terms as $term) {
        $links[] = sprintf(
            '<a href="%s">%s</a>',
            esc_url(get_term_link($term)),
            esc_html($term->name)
        );
    }
    
    echo implode($separator, $links);
}

// ========================================
// URL・リンク関連
// ========================================

/**
 * サービス一覧ページのURLを取得
 * 
 * @return string URL
 */
function get_services_archive_url() {
    return get_post_type_archive_link('tax_service');
}

/**
 * タクソノミーアーカイブURLを取得
 * 
 * @param string $taxonomy タクソノミー名
 * @param int $term_id ターム ID
 * @return string URL
 */
function get_taxonomy_archive_url($taxonomy, $term_id) {
    $term = get_term($term_id, $taxonomy);
    if (!$term || is_wp_error($term)) {
        return '';
    }
    return get_term_link($term);
}

// ========================================
// サービス数関連
// ========================================

/**
 * 掲載中のサービス総数を取得
 * 
 * @return int サービス総数
 */
function get_total_services_count() {
    $count = wp_cache_get('total_services_count', 'tax_matching');
    
    if (false === $count) {
        $args = [
            'post_type' => 'tax_service',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => [
                [
                    'key' => 'listing_status',
                    'value' => '1',
                    'compare' => '=',
                ],
            ],
        ];
        
        $query = new WP_Query($args);
        $count = $query->found_posts;
        
        wp_cache_set('total_services_count', $count, 'tax_matching', 3600);
    }
    
    return $count > 0 ? $count : 100; // デフォルト値として100を返す
}

// ========================================
// デバッグ・開発用
// ========================================

/**
 * デバッグ情報を出力（開発環境のみ）
 * 
 * @param mixed $data 出力するデータ
 * @param string $label ラベル
 */
function debug_output($data, $label = 'Debug') {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<pre style="background:#f5f5f5;padding:10px;border:1px solid #ddd;margin:10px 0;">';
        echo '<strong>' . esc_html($label) . ':</strong><br>';
        print_r($data);
        echo '</pre>';
    }
}


// ========================================
// 税理士事務所の得意分野・得意業種関連
// ========================================

/**
 * 全税理士事務所の得意分野を取得
 * 
 * @return array 得意分野の配列（重複なし、ソート済み）
 */
function get_all_office_services() {
    global $wpdb;
    
    $services = array();
    
    // すべての税理士事務所の得意分野を取得
    $results = $wpdb->get_results("
        SELECT meta_value 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_tax_office_services' 
        AND meta_value != ''
    ");
    
    foreach ($results as $result) {
        $services_array = json_decode($result->meta_value, true);
        if (is_array($services_array)) {
            $services = array_merge($services, $services_array);
        }
    }
    
    // 重複を削除してソート
    $services = array_unique($services);
    sort($services);
    
    return $services;
}

/**
 * 全税理士事務所の得意業種を取得
 * 
 * @return array 得意業種の配列（重複なし、ソート済み）
 */
function get_all_office_industries() {
    global $wpdb;
    
    $industries = array();
    
    // すべての税理士事務所の得意業種を取得
    $results = $wpdb->get_results("
        SELECT meta_value 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_tax_office_industries' 
        AND meta_value != ''
    ");
    
    foreach ($results as $result) {
        $industries_array = json_decode($result->meta_value, true);
        if (is_array($industries_array)) {
            $industries = array_merge($industries, $industries_array);
        }
    }
    
    // 重複を削除してソート
    $industries = array_unique($industries);
    sort($industries);
    
    return $industries;
}
