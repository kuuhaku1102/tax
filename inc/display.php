<?php
/**
 * Display Functions
 * 
 * 表示関連の関数を定義
 * 
 * 設計意図:
 * - テンプレートに表示ロジックを書かせない
 * - HTMLとPHPを分離
 * - 再利用可能なコンポーネント化
 * 
 * @package TaxMatchingTheme
 */

// ========================================
// サービスカード表示
// ========================================

/**
 * サービスカードを表示
 * 
 * 設計意図:
 * - 一覧ページで使用する統一されたカード表示
 * - ACFフィールドの存在チェックを内包
 * - HTMLをテンプレートに直書きしない
 * 
 * @param int $post_id 投稿ID
 * @param string $size 画像サイズ
 */
function display_service_card($post_id = null, $size = 'service-card') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // 掲載ステータスチェック
    if (!is_service_active($post_id)) {
        return;
    }
    
    $title = get_the_title($post_id);
    $permalink = get_permalink($post_id);
    $thumbnail = get_the_post_thumbnail($post_id, $size);
    $catchcopy = get_field_safe('catchcopy', $post_id);
    $listing_plan = get_field_safe('listing_plan', $post_id);
    $office_name = get_field_safe('office_name', $post_id);
    
    // タクソノミー情報
    $industries = get_taxonomy_terms_list('service_industry', $post_id);
    $issues = get_taxonomy_terms_list('service_issue', $post_id);
    
    ?>
    <div class="service-card" data-post-id="<?php echo esc_attr($post_id); ?>">
        <?php if ($listing_plan): ?>
            <div class="service-card__badge service-card__badge--<?php echo esc_attr(strtolower($listing_plan)); ?>">
                <?php echo esc_html(get_listing_plan_label($listing_plan)); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($thumbnail): ?>
            <div class="service-card__thumbnail">
                <a href="<?php echo esc_url($permalink); ?>">
                    <?php echo $thumbnail; ?>
                </a>
            </div>
        <?php endif; ?>
        
        <div class="service-card__content">
            <h3 class="service-card__title">
                <a href="<?php echo esc_url($permalink); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            </h3>
            
            <?php if ($office_name): ?>
                <div class="service-card__office">
                    <?php echo esc_html($office_name); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($catchcopy): ?>
                <div class="service-card__catchcopy">
                    <?php echo esc_html($catchcopy); ?>
                </div>
            <?php endif; ?>
            
            <div class="service-card__meta">
                <?php if ($industries): ?>
                    <div class="service-card__meta-item">
                        <span class="service-card__meta-label">業種:</span>
                        <span class="service-card__meta-value"><?php echo esc_html($industries); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($issues): ?>
                    <div class="service-card__meta-item">
                        <span class="service-card__meta-label">課題:</span>
                        <span class="service-card__meta-value"><?php echo esc_html($issues); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="service-card__action">
                <a href="<?php echo esc_url($permalink); ?>" class="service-card__button">
                    詳細を見る
                </a>
            </div>
        </div>
    </div>
    <?php
}

// ========================================
// サービス詳細情報表示
// ========================================

/**
 * サービス詳細情報を表示
 * 
 * @param int $post_id 投稿ID
 */
function display_service_detail($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $office_name = get_field_safe('office_name', $post_id);
    $representative_name = get_field_safe('representative_name', $post_id);
    $office_url = get_field_safe('office_url', $post_id);
    $office_tel = get_field_safe('office_tel', $post_id);
    $office_email = get_field_safe('office_email', $post_id);
    $office_address = get_field_safe('office_address', $post_id);
    
    ?>
    <div class="service-detail">
        <div class="service-detail__basic">
            <h2 class="service-detail__heading">基本情報</h2>
            
            <table class="service-detail__table">
                <?php if ($office_name): ?>
                    <tr>
                        <th>事務所名</th>
                        <td><?php echo esc_html($office_name); ?></td>
                    </tr>
                <?php endif; ?>
                
                <?php if ($representative_name): ?>
                    <tr>
                        <th>代表者名</th>
                        <td><?php echo esc_html($representative_name); ?></td>
                    </tr>
                <?php endif; ?>
                
                <?php if ($office_address): ?>
                    <tr>
                        <th>所在地</th>
                        <td><?php echo nl2br(esc_html($office_address)); ?></td>
                    </tr>
                <?php endif; ?>
                
                <?php if ($office_tel): ?>
                    <tr>
                        <th>電話番号</th>
                        <td><a href="tel:<?php echo esc_attr(str_replace(['-', ' '], '', $office_tel)); ?>"><?php echo esc_html($office_tel); ?></a></td>
                    </tr>
                <?php endif; ?>
                
                <?php if ($office_email): ?>
                    <tr>
                        <th>メールアドレス</th>
                        <td><a href="mailto:<?php echo esc_attr($office_email); ?>"><?php echo esc_html($office_email); ?></a></td>
                    </tr>
                <?php endif; ?>
                
                <?php if ($office_url): ?>
                    <tr>
                        <th>公式サイト</th>
                        <td><a href="<?php echo esc_url($office_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($office_url); ?></a></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <?php
        $service_features = get_field_safe('service_features', $post_id);
        if ($service_features):
        ?>
            <div class="service-detail__features">
                <h2 class="service-detail__heading">サービスの特徴</h2>
                <div class="service-detail__content">
                    <?php echo wp_kses_post($service_features); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        $editor_good_points = get_field_safe('editor_good_points', $post_id);
        if ($editor_good_points):
        ?>
            <div class="service-detail__editor-review">
                <h2 class="service-detail__heading">編集部のおすすめポイント</h2>
                <div class="service-detail__content">
                    <?php echo wp_kses_post($editor_good_points); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        $pricing_info = get_field_safe('pricing_info', $post_id);
        if ($pricing_info):
        ?>
            <div class="service-detail__pricing">
                <h2 class="service-detail__heading">料金情報</h2>
                <div class="service-detail__content">
                    <?php echo wp_kses_post($pricing_info); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// ========================================
// タクソノミーフィルター表示
// ========================================

/**
 * タクソノミーフィルターを表示
 * 
 * 設計意図:
 * - 一覧ページでの絞り込みフィルター
 * - GETパラメータとの連携
 * 
 * @param string $taxonomy タクソノミー名
 * @param string $label ラベル
 */
function display_taxonomy_filter($taxonomy, $label) {
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => true,
    ]);
    
    if (empty($terms) || is_wp_error($terms)) {
        return;
    }
    
    $current_term = isset($_GET[$taxonomy]) ? intval($_GET[$taxonomy]) : 0;
    
    ?>
    <div class="taxonomy-filter">
        <label class="taxonomy-filter__label"><?php echo esc_html($label); ?></label>
        <select name="<?php echo esc_attr($taxonomy); ?>" class="taxonomy-filter__select">
            <option value="">すべて</option>
            <?php foreach ($terms as $term): ?>
                <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected($current_term, $term->term_id); ?>>
                    <?php echo esc_html($term->name); ?> (<?php echo esc_html($term->count); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}

// ========================================
// パンくずリスト表示
// ========================================

/**
 * パンくずリストを表示
 * 
 * @param int $post_id 投稿ID
 */
function display_breadcrumb($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    ?>
    <nav class="breadcrumb" aria-label="パンくずリスト">
        <ol class="breadcrumb__list">
            <li class="breadcrumb__item">
                <a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a>
            </li>
            
            <?php if (is_singular('tax_service')): ?>
                <li class="breadcrumb__item">
                    <a href="<?php echo esc_url(get_services_archive_url()); ?>">税理士サービス一覧</a>
                </li>
                <li class="breadcrumb__item" aria-current="page">
                    <?php echo esc_html(get_the_title($post_id)); ?>
                </li>
            <?php elseif (is_post_type_archive('tax_service')): ?>
                <li class="breadcrumb__item" aria-current="page">
                    税理士サービス一覧
                </li>
            <?php elseif (is_tax()): ?>
                <li class="breadcrumb__item">
                    <a href="<?php echo esc_url(get_services_archive_url()); ?>">税理士サービス一覧</a>
                </li>
                <li class="breadcrumb__item" aria-current="page">
                    <?php single_term_title(); ?>
                </li>
            <?php endif; ?>
        </ol>
    </nav>
    <?php
}

// ========================================
// ページネーション表示
// ========================================

/**
 * ページネーションを表示
 * 
 * @param WP_Query $query クエリオブジェクト
 */
function display_pagination($query = null) {
    global $wp_query;
    
    if (!$query) {
        $query = $wp_query;
    }
    
    $big = 999999999;
    
    $pagination = paginate_links([
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $query->max_num_pages,
        'prev_text' => '&laquo; 前へ',
        'next_text' => '次へ &raquo;',
        'type' => 'list',
    ]);
    
    if ($pagination) {
        echo '<nav class="pagination" aria-label="ページネーション">';
        echo $pagination;
        echo '</nav>';
    }
}
