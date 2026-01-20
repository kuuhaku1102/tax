<?php
/**
 * Admin Functions
 * 
 * 管理画面のカスタマイズ関数を定義
 * 
 * 設計意図:
 * - 管理画面を使いやすくする
 * - 運用者の作業効率を向上させる
 * - 事故を防ぐための制約を設ける
 * 
 * @package TaxMatchingTheme
 */

// ========================================
// 管理画面カラムのカスタマイズ
// ========================================

/**
 * tax_service の管理画面カラムを追加
 * 
 * 設計意図:
 * - 一覧画面で重要な情報を表示
 * - 掲載ステータス・プラン・優先度を一目で確認
 */
add_filter('manage_tax_service_posts_columns', function($columns) {
    $new_columns = [];
    
    // チェックボックスとタイトルは維持
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    
    // カスタムカラムを追加
    $new_columns['thumbnail'] = 'サムネイル';
    $new_columns['listing_status'] = '掲載ステータス';
    $new_columns['listing_plan'] = '掲載プラン';
    $new_columns['priority_score'] = '優先度';
    $new_columns['office_name'] = '事務所名';
    
    // タクソノミーカラムを追加
    $new_columns['taxonomy-service_industry'] = '業種';
    $new_columns['taxonomy-service_issue'] = '課題';
    
    // 日付は最後に
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
});

/**
 * カスタムカラムの内容を表示
 */
add_action('manage_tax_service_posts_custom_column', function($column, $post_id) {
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, [60, 60]);
            } else {
                echo '<span style="color:#999;">なし</span>';
            }
            break;
            
        case 'listing_status':
            $status = get_field('listing_status', $post_id);
            if ($status) {
                echo '<span style="color:#46b450;font-weight:bold;">● 掲載中</span>';
            } else {
                echo '<span style="color:#dc3232;">○ 非掲載</span>';
            }
            break;
            
        case 'listing_plan':
            $plan = get_field('listing_plan', $post_id);
            if ($plan) {
                $label = get_listing_plan_label($plan);
                $color = [
                    'premium' => '#f0ad4e',
                    'standard' => '#5bc0de',
                    'basic' => '#999',
                ];
                $plan_lower = strtolower($plan);
                $bg_color = isset($color[$plan_lower]) ? $color[$plan_lower] : '#999';
                
                echo sprintf(
                    '<span style="background:%s;color:#fff;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:bold;">%s</span>',
                    esc_attr($bg_color),
                    esc_html($label)
                );
            } else {
                echo '<span style="color:#999;">未設定</span>';
            }
            break;
            
        case 'priority_score':
            $score = get_field('priority_score', $post_id);
            if ($score) {
                echo '<strong>' . esc_html($score) . '</strong>';
            } else {
                echo '<span style="color:#999;">0</span>';
            }
            break;
            
        case 'office_name':
            $office_name = get_field('office_name', $post_id);
            if ($office_name) {
                echo esc_html($office_name);
            } else {
                echo '<span style="color:#999;">未設定</span>';
            }
            break;
    }
}, 10, 2);

/**
 * カラムの並び替えを有効化
 */
add_filter('manage_edit-tax_service_sortable_columns', function($columns) {
    $columns['listing_status'] = 'listing_status';
    $columns['listing_plan'] = 'listing_plan';
    $columns['priority_score'] = 'priority_score';
    return $columns;
});

/**
 * 並び替えのクエリを調整
 */
add_action('pre_get_posts', function($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    switch ($orderby) {
        case 'listing_status':
            $query->set('meta_key', 'listing_status');
            $query->set('orderby', 'meta_value_num');
            break;
            
        case 'listing_plan':
            $query->set('meta_key', 'listing_plan');
            $query->set('orderby', 'meta_value');
            break;
            
        case 'priority_score':
            $query->set('meta_key', 'priority_score');
            $query->set('orderby', 'meta_value_num');
            break;
    }
});

// ========================================
// 管理画面フィルター
// ========================================

/**
 * 管理画面にフィルターを追加
 * 
 * 設計意図:
 * - 掲載ステータス・プランで絞り込み
 * - 運用者の作業効率を向上
 */
add_action('restrict_manage_posts', function($post_type) {
    if ($post_type !== 'tax_service') {
        return;
    }
    
    // 掲載ステータスフィルター
    $listing_status = isset($_GET['listing_status']) ? $_GET['listing_status'] : '';
    ?>
    <select name="listing_status">
        <option value="">すべての掲載ステータス</option>
        <option value="1" <?php selected($listing_status, '1'); ?>>掲載中</option>
        <option value="0" <?php selected($listing_status, '0'); ?>>非掲載</option>
    </select>
    <?php
    
    // 掲載プランフィルター
    $listing_plan = isset($_GET['listing_plan']) ? $_GET['listing_plan'] : '';
    ?>
    <select name="listing_plan">
        <option value="">すべての掲載プラン</option>
        <option value="premium" <?php selected($listing_plan, 'premium'); ?>>プレミアム</option>
        <option value="standard" <?php selected($listing_plan, 'standard'); ?>>スタンダード</option>
        <option value="basic" <?php selected($listing_plan, 'basic'); ?>>ベーシック</option>
    </select>
    <?php
});

/**
 * フィルターのクエリを調整
 */
add_filter('parse_query', function($query) {
    global $pagenow;
    
    if (!is_admin() || $pagenow !== 'edit.php' || !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] !== 'tax_service') {
        return;
    }
    
    // 掲載ステータスフィルター
    if (isset($_GET['listing_status']) && $_GET['listing_status'] !== '') {
        $query->query_vars['meta_query'][] = [
            'key' => 'listing_status',
            'value' => sanitize_text_field($_GET['listing_status']),
            'compare' => '=',
        ];
    }
    
    // 掲載プランフィルター
    if (isset($_GET['listing_plan']) && $_GET['listing_plan'] !== '') {
        $query->query_vars['meta_query'][] = [
            'key' => 'listing_plan',
            'value' => sanitize_text_field($_GET['listing_plan']),
            'compare' => '=',
        ];
    }
});

// ========================================
// 管理画面メッセージのカスタマイズ
// ========================================

/**
 * 投稿更新メッセージをカスタマイズ
 */
add_filter('post_updated_messages', function($messages) {
    global $post;
    
    $messages['tax_service'] = [
        0 => '',
        1 => '税理士サービスを更新しました。',
        2 => 'カスタムフィールドを更新しました。',
        3 => 'カスタムフィールドを削除しました。',
        4 => '税理士サービスを更新しました。',
        5 => isset($_GET['revision']) ? sprintf('リビジョン %s を復元しました。', wp_post_revision_title((int) $_GET['revision'], false)) : false,
        6 => '税理士サービスを公開しました。',
        7 => '税理士サービスを保存しました。',
        8 => '税理士サービスを送信しました。',
        9 => sprintf(
            '税理士サービスを予約しました。公開予定: <strong>%s</strong>',
            date_i18n('Y年n月j日 H:i', strtotime($post->post_date))
        ),
        10 => '税理士サービスの下書きを更新しました。',
    ];
    
    return $messages;
});

// ========================================
// 管理画面ヘルプテキスト
// ========================================

/**
 * 管理画面にヘルプテキストを追加
 */
add_action('admin_head-post.php', function() {
    global $post_type;
    
    if ($post_type !== 'tax_service') {
        return;
    }
    
    ?>
    <style>
        .tax-service-help {
            background: #fff;
            border-left: 4px solid #2271b1;
            padding: 12px;
            margin: 20px 0;
        }
        .tax-service-help h4 {
            margin: 0 0 8px;
            font-size: 14px;
        }
        .tax-service-help ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
    <script>
        jQuery(document).ready(function($) {
            var helpText = '<div class="tax-service-help">' +
                '<h4>📝 編集のポイント</h4>' +
                '<ul>' +
                '<li><strong>掲載ステータス</strong>: ONにすると公開ページに表示されます</li>' +
                '<li><strong>優先度スコア</strong>: 数値が大きいほど上位に表示されます</li>' +
                '<li><strong>掲載プラン</strong>: プレミアム > スタンダード > ベーシックの順で優先されます</li>' +
                '<li><strong>業種・課題</strong>: マッチング精度に影響します。適切に設定してください</li>' +
                '</ul>' +
                '</div>';
            
            $('#titlediv').after(helpText);
        });
    </script>
    <?php
});

// ========================================
// クイック編集の無効化（事故防止）
// ========================================

/**
 * クイック編集を無効化
 * 
 * 設計意図:
 * - ACFフィールドが多いため、クイック編集での誤操作を防ぐ
 * - 通常の編集画面を使用させる
 */
add_filter('post_row_actions', function($actions, $post) {
    if ($post->post_type === 'tax_service') {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}, 10, 2);

// ========================================
// ダッシュボードウィジェット
// ========================================

/**
 * ダッシュボードに統計ウィジェットを追加
 */
add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'tax_service_stats',
        '税理士サービス統計',
        'display_tax_service_stats_widget'
    );
});

/**
 * 統計ウィジェットの内容を表示
 */
function display_tax_service_stats_widget() {
    $total = wp_count_posts('tax_service')->publish;
    
    $active_count = count(get_posts([
        'post_type' => 'tax_service',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'listing_status',
                'value' => '1',
                'compare' => '=',
            ],
        ],
        'fields' => 'ids',
    ]));
    
    $premium_count = count(get_posts([
        'post_type' => 'tax_service',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'listing_plan',
                'value' => 'premium',
                'compare' => '=',
            ],
        ],
        'fields' => 'ids',
    ]));
    
    ?>
    <div class="tax-service-stats">
        <style>
            .tax-service-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
            .tax-service-stat {
                text-align: center;
                padding: 15px;
                background: #f0f0f1;
                border-radius: 4px;
            }
            .tax-service-stat__number {
                font-size: 32px;
                font-weight: bold;
                color: #2271b1;
                display: block;
            }
            .tax-service-stat__label {
                font-size: 13px;
                color: #646970;
                margin-top: 5px;
            }
        </style>
        
        <div class="tax-service-stat">
            <span class="tax-service-stat__number"><?php echo esc_html($total); ?></span>
            <span class="tax-service-stat__label">総サービス数</span>
        </div>
        
        <div class="tax-service-stat">
            <span class="tax-service-stat__number"><?php echo esc_html($active_count); ?></span>
            <span class="tax-service-stat__label">掲載中</span>
        </div>
        
        <div class="tax-service-stat">
            <span class="tax-service-stat__number"><?php echo esc_html($premium_count); ?></span>
            <span class="tax-service-stat__label">プレミアムプラン</span>
        </div>
    </div>
    
    <p style="margin-top: 15px;">
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=tax_service')); ?>" class="button button-primary">
            税理士サービスを管理
        </a>
    </p>
    <?php
}

// ========================================
// メタボックスをエディター内に移動
// ========================================

/**
 * サイドバーのメタボックスを削除
 */
add_action('do_meta_boxes', function() {
    // デフォルトのカスタムフィールドメタボックスを削除
    remove_meta_box('postcustom', 'tax_service', 'normal');
    remove_meta_box('postcustom', 'tax_service', 'side');
    
    // タクソノミーメタボックスを削除
    remove_meta_box('service_industrydiv', 'tax_service', 'side');
    remove_meta_box('service_issuediv', 'tax_service', 'side');
    remove_meta_box('service_areadiv', 'tax_service', 'side');
});

/**
 * エディター内にメタボックスを追加
 */
add_action('add_meta_boxes', function() {
    // タクソノミーメタボックス
    add_meta_box(
        'service_industry_box',
        '対応業種',
        'post_categories_meta_box',
        'tax_service',
        'normal',
        'default',
        array('taxonomy' => 'service_industry')
    );
    
    add_meta_box(
        'service_issue_box',
        '対応課題',
        'post_categories_meta_box',
        'tax_service',
        'normal',
        'default',
        array('taxonomy' => 'service_issue')
    );
    
    add_meta_box(
        'service_area_box',
        '対応エリア',
        'post_categories_meta_box',
        'tax_service',
        'normal',
        'default',
        array('taxonomy' => 'service_area')
    );
});

/**
 * 管理画面のスタイル調整
 */
add_action('admin_head-post.php', function() {
    global $post_type;
    if ($post_type !== 'tax_service') {
        return;
    }
    ?>
    <style>
        /* タクソノミーメタボックスのスタイル */
        #service_industry_box,
        #service_issue_box,
        #service_area_box {
            border-left: 4px solid #0066cc;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        #service_industry_box .hndle,
        #service_issue_box .hndle,
        #service_area_box .hndle {
            background: #f8f9fa;
            font-weight: 600;
            color: #0066cc;
        }
        
        #service_industry_box .inside,
        #service_issue_box .inside,
        #service_area_box .inside {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
    <?php
});
