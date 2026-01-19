<?php
/**
 * Query Functions
 * 
 * クエリ関連の関数を定義
 * 
 * 設計意図:
 * - WP_Queryの複雑な条件をテンプレートに書かせない
 * - 並び順・フィルタリングロジックを一元管理
 * 
 * @package TaxMatchingTheme
 */

// ========================================
// 優先度順取得
// ========================================

/**
 * 優先度順にサービスを取得
 * 
 * 設計意図:
 * - priority_score（数値）で並び替え
 * - 掲載ステータスが有効なもののみ取得
 * - 将来的なスコアリングロジック変更に対応
 * 
 * @param array $args 追加のクエリ引数
 * @return WP_Query
 */
function get_priority_services($args = []) {
    $default_args = [
        'post_type' => 'tax_service',
        'posts_per_page' => 10,
        'meta_query' => [
            'relation' => 'AND',
            // 掲載ステータスがONのもの
            [
                'key' => 'listing_status',
                'value' => '1',
                'compare' => '=',
            ],
        ],
        'meta_key' => 'priority_score',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    ];
    
    $query_args = array_merge($default_args, $args);
    return new WP_Query($query_args);
}

// ========================================
// マッチング条件での取得
// ========================================

/**
 * マッチング条件でサービスを取得
 * 
 * 設計意図:
 * - 業種・課題・エリアなどの条件でフィルタリング
 * - 完全一致ではなく「いずれか一致」で表示ゼロを防ぐ
 * - 優先度スコアで並び替え
 * 
 * @param array $conditions マッチング条件
 * @return WP_Query
 */
function get_matched_services($conditions = []) {
    $args = [
        'post_type' => 'tax_service',
        'posts_per_page' => 20,
        'meta_query' => [
            [
                'key' => 'listing_status',
                'value' => '1',
                'compare' => '=',
            ],
        ],
        'meta_key' => 'priority_score',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    ];
    
    // タクソノミー条件の追加
    if (!empty($conditions['taxonomies'])) {
        $tax_query = ['relation' => 'OR'];
        
        foreach ($conditions['taxonomies'] as $taxonomy => $term_ids) {
            if (!empty($term_ids)) {
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $term_ids,
                    'operator' => 'IN',
                ];
            }
        }
        
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
    }
    
    return new WP_Query($args);
}

// ========================================
// 注目サービスの取得
// ========================================

/**
 * 注目サービスを取得
 * 
 * 設計意図:
 * - トップページやサイドバーでの表示用
 * - featured フラグがONのものを優先度順に取得
 * 
 * @param int $count 取得件数
 * @return WP_Query
 */
function get_featured_services($count = 5) {
    $args = [
        'post_type' => 'tax_service',
        'posts_per_page' => $count,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'listing_status',
                'value' => '1',
                'compare' => '=',
            ],
            [
                'key' => 'featured',
                'value' => '1',
                'compare' => '=',
            ],
        ],
        'meta_key' => 'priority_score',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    ];
    
    return new WP_Query($args);
}

// ========================================
// プラン別取得
// ========================================

/**
 * 掲載プラン別にサービスを取得
 * 
 * @param string $plan プラン名（premium/standard/basic）
 * @param int $count 取得件数
 * @return WP_Query
 */
function get_services_by_plan($plan, $count = 10) {
    $args = [
        'post_type' => 'tax_service',
        'posts_per_page' => $count,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'listing_status',
                'value' => '1',
                'compare' => '=',
            ],
            [
                'key' => 'listing_plan',
                'value' => $plan,
                'compare' => '=',
            ],
        ],
        'meta_key' => 'priority_score',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    ];
    
    return new WP_Query($args);
}

// ========================================
// 検索クエリの構築
// ========================================

/**
 * GETパラメータから検索クエリを構築
 * 
 * 設計意図:
 * - URLパラメータをホワイトリスト制御
 * - SQLインジェクション対策
 * - 将来的な検索条件追加に対応
 * 
 * @return array クエリ引数
 */
function build_search_query_from_params() {
    $args = [
        'post_type' => 'tax_service',
        'posts_per_page' => 20,
        'meta_query' => [
            [
                'key' => 'listing_status',
                'value' => '1',
                'compare' => '=',
            ],
        ],
    ];
    
    // ホワイトリスト定義
    $allowed_taxonomies = [
        'service_industry',
        'service_issue',
        'service_phase',
        'service_area',
        'service_tag',
    ];
    
    $tax_query = ['relation' => 'AND'];
    
    foreach ($allowed_taxonomies as $taxonomy) {
        if (isset($_GET[$taxonomy]) && !empty($_GET[$taxonomy])) {
            $term_ids = array_map('intval', (array) $_GET[$taxonomy]);
            
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term_ids,
                'operator' => 'IN',
            ];
        }
    }
    
    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }
    
    // 並び順の指定
    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'priority';
    
    switch ($orderby) {
        case 'date':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
        case 'title':
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
            break;
        case 'priority':
        default:
            $args['meta_key'] = 'priority_score';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
    }
    
    return $args;
}

// ========================================
// 関連サービスの取得
// ========================================

/**
 * 関連サービスを取得
 * 
 * 設計意図:
 * - 詳細ページでの「関連サービス」表示用
 * - 同じ業種または課題のサービスを取得
 * - 現在のサービスは除外
 * 
 * @param int $post_id 投稿ID
 * @param int $count 取得件数
 * @return WP_Query
 */
function get_related_services($post_id = null, $count = 5) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // 業種と課題のタームを取得
    $industries = wp_get_post_terms($post_id, 'service_industry', ['fields' => 'ids']);
    $issues = wp_get_post_terms($post_id, 'service_issue', ['fields' => 'ids']);
    
    $args = [
        'post_type' => 'tax_service',
        'posts_per_page' => $count,
        'post__not_in' => [$post_id],
        'meta_query' => [
            [
                'key' => 'listing_status',
                'value' => '1',
                'compare' => '=',
            ],
        ],
        'meta_key' => 'priority_score',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    ];
    
    // タクソノミー条件の追加
    $tax_query = ['relation' => 'OR'];
    
    if (!empty($industries)) {
        $tax_query[] = [
            'taxonomy' => 'service_industry',
            'field' => 'term_id',
            'terms' => $industries,
            'operator' => 'IN',
        ];
    }
    
    if (!empty($issues)) {
        $tax_query[] = [
            'taxonomy' => 'service_issue',
            'field' => 'term_id',
            'terms' => $issues,
            'operator' => 'IN',
        ];
    }
    
    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }
    
    return new WP_Query($args);
}
