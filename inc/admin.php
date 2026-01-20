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
    
    // タクソノミーメタボックスをサイドバーから削除（階層型）
    remove_meta_box('service_industrydiv', 'tax_service', 'side');
    remove_meta_box('service_industrydiv', 'tax_service', 'normal');
    remove_meta_box('service_issuediv', 'tax_service', 'side');
    remove_meta_box('service_issuediv', 'tax_service', 'normal');
    remove_meta_box('service_areadiv', 'tax_service', 'side');
    remove_meta_box('service_areadiv', 'tax_service', 'normal');
    remove_meta_box('service_phasediv', 'tax_service', 'side');
    remove_meta_box('service_phasediv', 'tax_service', 'normal');
    
    // タグ型タクソノミーを削除
    remove_meta_box('tagsdiv-service_tag', 'tax_service', 'side');
    remove_meta_box('tagsdiv-service_tag', 'tax_service', 'normal');
    
    // その他のサイドバーメタボックスを削除
    remove_meta_box('submitdiv', 'tax_service', 'side'); // 公開
    remove_meta_box('slugdiv', 'tax_service', 'side'); // スラッグ
    remove_meta_box('categorydiv', 'tax_service', 'side'); // カテゴリー
    remove_meta_box('tagsdiv-post_tag', 'tax_service', 'side'); // タグ
    remove_meta_box('postimagediv', 'tax_service', 'side'); // アイキャッチ画像
    remove_meta_box('pageparentdiv', 'tax_service', 'side'); // ページ属性
    remove_meta_box('commentstatusdiv', 'tax_service', 'side'); // ディスカッション
    remove_meta_box('commentsdiv', 'tax_service', 'side'); // コメント
    remove_meta_box('authordiv', 'tax_service', 'side'); // 作成者
    remove_meta_box('revisionsdiv', 'tax_service', 'side'); // リビジョン
});

/**
 * エディター内にメタボックスを追加
 */
add_action('add_meta_boxes', function() {
    // 掲載制御メタボックス
    add_meta_box(
        'listing_control_box',
        '掲載制御',
        'render_listing_control_meta_box',
        'tax_service',
        'normal',
        'high'
    );
    
    // タクソノミーメタボックス
    add_meta_box(
        'service_industry_box',
        '対応業種',
        'render_taxonomy_meta_box',
        'tax_service',
        'normal',
        'default',
        array('taxonomy' => 'service_industry')
    );
    
    add_meta_box(
        'service_issue_box',
        '対応課題',
        'render_taxonomy_meta_box',
        'tax_service',
        'normal',
        'default',
        array('taxonomy' => 'service_issue')
    );
    
    add_meta_box(
        'service_area_box',
        '対応エリア',
        'render_taxonomy_meta_box',
        'tax_service',
        'normal',
        'default',
        array('taxonomy' => 'service_area')
    );
});

/**
 * 掲載制御メタボックスの表示
 */
function render_listing_control_meta_box($post) {
    wp_nonce_field('listing_control_meta_box', 'listing_control_nonce');
    
    $listing_status = get_post_meta($post->ID, 'listing_status', true);
    $featured_service = get_post_meta($post->ID, 'featured_service', true);
    $listing_plan = get_post_meta($post->ID, 'listing_plan', true);
    $priority_score = get_post_meta($post->ID, 'priority_score', true);
    $listing_start_date = get_post_meta($post->ID, 'listing_start_date', true);
    $listing_end_date = get_post_meta($post->ID, 'listing_end_date', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="listing_status">掲載ステータス</label></th>
            <td>
                <label>
                    <input type="checkbox" name="listing_status" id="listing_status" value="1" <?php checked($listing_status, '1'); ?>>
                    ONにするとサイトに表示されます
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="featured_service">注目サービス</label></th>
            <td>
                <label>
                    <input type="checkbox" name="featured_service" id="featured_service" value="1" <?php checked($featured_service, '1'); ?>>
                    注目サービスにする
                </label>
                <p class="description">トップページや一覧ページで目立つ位置に表示されます</p>
            </td>
        </tr>
        <tr>
            <th><label for="listing_plan">掲載プラン</label></th>
            <td>
                <select name="listing_plan" id="listing_plan">
                    <option value="basic" <?php selected($listing_plan, 'basic'); ?>>ベーシック</option>
                    <option value="standard" <?php selected($listing_plan, 'standard'); ?>>スタンダード</option>
                    <option value="premium" <?php selected($listing_plan, 'premium'); ?>>プレミアム</option>
                </select>
                <p class="description">プレミアム &gt; スタンダード &gt; ベーシックの順で優先表示されます</p>
            </td>
        </tr>
        <tr>
            <th><label for="priority_score">優先度スコア</label></th>
            <td>
                <input type="number" name="priority_score" id="priority_score" value="<?php echo esc_attr($priority_score); ?>" min="0" max="1000">
                <p class="description">数値が大きいほど上位に表示されます (0-1000)</p>
            </td>
        </tr>
        <tr>
            <th><label for="listing_start_date">掲載開始日</label></th>
            <td>
                <input type="date" name="listing_start_date" id="listing_start_date" value="<?php echo esc_attr($listing_start_date); ?>">
                <p class="description">掲載を開始する日付を選択してください (空欄の場合は即時掲載)</p>
            </td>
        </tr>
        <tr>
            <th><label for="listing_end_date">掲載終了日</label></th>
            <td>
                <input type="date" name="listing_end_date" id="listing_end_date" value="<?php echo esc_attr($listing_end_date); ?>">
                <p class="description">掲載を終了する日付を選択してください (空欄の場合は無期限)</p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * 掲載制御メタボックスの保存
 */
add_action('save_post_tax_service', function($post_id) {
    if (!isset($_POST['listing_control_nonce']) || !wp_verify_nonce($_POST['listing_control_nonce'], 'listing_control_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // 掲載ステータス
    $listing_status = isset($_POST['listing_status']) ? '1' : '0';
    update_post_meta($post_id, 'listing_status', $listing_status);
    
    // 注目サービス
    $featured_service = isset($_POST['featured_service']) ? '1' : '0';
    update_post_meta($post_id, 'featured_service', $featured_service);
    
    // 掲載プラン
    if (isset($_POST['listing_plan'])) {
        update_post_meta($post_id, 'listing_plan', sanitize_text_field($_POST['listing_plan']));
    }
    
    // 優先度スコア
    if (isset($_POST['priority_score'])) {
        update_post_meta($post_id, 'priority_score', intval($_POST['priority_score']));
    }
    
    // 掲載開始日
    if (isset($_POST['listing_start_date'])) {
        update_post_meta($post_id, 'listing_start_date', sanitize_text_field($_POST['listing_start_date']));
    }
    
    // 掲載終了日
    if (isset($_POST['listing_end_date'])) {
        update_post_meta($post_id, 'listing_end_date', sanitize_text_field($_POST['listing_end_date']));
    }
});

/**
 * タクソノミーメタボックスの表示
 */
function render_taxonomy_meta_box($post, $box) {
    $taxonomy = $box['args']['taxonomy'];
    $tax = get_taxonomy($taxonomy);
    ?>
    <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
        <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
            <?php
            $name = ($taxonomy == 'category') ? 'post_category' : 'tax_input[' . $taxonomy . ']';
            echo "<input type='hidden' name='{$name}[]' value='0' />";
            ?>
            <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:<?php echo $taxonomy; ?>" class="categorychecklist form-no-clear">
                <?php
                wp_terms_checklist($post->ID, array(
                    'taxonomy' => $taxonomy,
                    'popular_cats' => false
                ));
                ?>
            </ul>
        </div>
        <div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
            <a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="taxonomy-add-new">
                + <?php echo $tax->labels->add_new_item; ?>
            </a>
            <p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
                <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
                <input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="" aria-required="true"/>
                <input type="button" id="<?php echo $taxonomy; ?>-add-submit" data-wp-lists="add:<?php echo $taxonomy; ?>checklist:<?php echo $taxonomy; ?>-add" class="button category-add-submit" value="<?php echo esc_attr($tax->labels->add_new_item); ?>"/>
                <?php wp_nonce_field('add-' . $taxonomy, '_ajax_nonce-add-' . $taxonomy, false); ?>
                <span id="<?php echo $taxonomy; ?>-ajax-response"></span>
            </p>
        </div>
    </div>
    <?php
}

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
        /* 掲載制御メタボックスのスタイル */
        #listing_control_box {
            border-left: 4px solid #0066cc;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        #listing_control_box .hndle {
            background: #f8f9fa;
            font-weight: 600;
            color: #0066cc;
        }
        
        #listing_control_box .form-table th {
            width: 200px;
            font-weight: 600;
        }
        
        #listing_control_box .form-table input[type="number"],
        #listing_control_box .form-table input[type="date"],
        #listing_control_box .form-table select {
            width: 300px;
        }
        
        #listing_control_box .description {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }
        
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
