<?php
/**
 * Template Name: 税理士サービス一覧ページ（統合版）
 * Template Post Type: tax_service
 * 
 * 設計思想:
 * - 税理士サービスと税理士事務所を統合表示
 * - 検索・フィルタリング機能を実装
 * - 並び順はクエリ関数で制御
 * - ページネーション対応
 * - 使いやすいUIデザイン
 * 
 * @package TaxMatchingTheme
 */

get_header(); ?>

<main class="site-main archive-page">
    
    <!-- パンくずリスト -->
    <?php if (function_exists('display_breadcrumb')) display_breadcrumb(); ?>
    
    <!-- ページヘッダー -->
    <header class="archive-header">
        <div class="container">
            <h1 class="archive-header__title">税理士を探す</h1>
            <p class="archive-header__description">
                業種・課題・エリアから、あなたに最適な税理士サービス・事務所を見つけましょう
            </p>
        </div>
    </header>
    
    <div class="container">
        
        <!-- 検索・フィルターフォーム -->
        <div class="service-filter">
            <form method="get" action="<?php echo esc_url(get_post_type_archive_link('tax_service')); ?>" class="service-filter__form">
                
                <div class="service-filter__grid">
                    <?php if (function_exists('display_taxonomy_filter')): ?>
                        <?php display_taxonomy_filter('service_industry', '業種'); ?>
                        <?php display_taxonomy_filter('service_issue', '課題・目的'); ?>
                        <?php display_taxonomy_filter('service_area', '対応エリア'); ?>
                    <?php endif; ?>
                    
                    <!-- 都道府県フィルター（税理士事務所用） -->
                    <div class="service-filter__field">
                        <label class="service-filter__label">都道府県</label>
                        <select name="office_prefecture" class="service-filter__select">
                            <option value="">すべて</option>
                            <?php
                            $prefectures = get_terms(array(
                                'taxonomy' => 'office_prefecture',
                                'hide_empty' => false,
                            ));
                            if ($prefectures && !is_wp_error($prefectures)):
                                foreach ($prefectures as $prefecture):
                                    $selected = isset($_GET['office_prefecture']) && $_GET['office_prefecture'] == $prefecture->slug ? 'selected' : '';
                            ?>
                                    <option value="<?php echo esc_attr($prefecture->slug); ?>" <?php echo $selected; ?>>
                                        <?php echo esc_html($prefecture->name); ?>
                                    </option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    
                    <!-- 並び順 -->
                    <div class="service-filter__field">
                        <label class="service-filter__label">並び順</label>
                        <select name="orderby" class="service-filter__select">
                            <option value="priority" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'priority'); ?>>おすすめ順</option>
                            <option value="date" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'date'); ?>>新着順</option>
                            <option value="title" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'title'); ?>>名前順</option>
                        </select>
                    </div>
                </div>
                
                <div class="service-filter__actions">
                    <button type="submit" class="button button--primary">
                        <span class="button__icon">🔍</span>
                        検索する
                    </button>
                    <a href="<?php echo esc_url(get_post_type_archive_link('tax_service')); ?>" class="button button--secondary">
                        条件をクリア
                    </a>
                </div>
                
            </form>
        </div>
        
        <!-- 税理士サービスセクション -->
        <section class="service-section">
            <h2 class="section-title">
                <span class="section-title__icon">💼</span>
                税理士サービス
            </h2>
            
            <div class="service-archive">
                
                <?php
                // 検索クエリの構築
                $query_args = array(
                    'post_type' => 'tax_service',
                    'posts_per_page' => 6,
                    'orderby' => isset($_GET['orderby']) ? $_GET['orderby'] : 'date',
                    'order' => 'DESC',
                );
                
                // タクソノミーフィルター
                $tax_query = array('relation' => 'AND');
                
                if (isset($_GET['service_industry']) && !empty($_GET['service_industry'])) {
                    $tax_query[] = array(
                        'taxonomy' => 'service_industry',
                        'field' => 'slug',
                        'terms' => $_GET['service_industry'],
                    );
                }
                
                if (isset($_GET['service_issue']) && !empty($_GET['service_issue'])) {
                    $tax_query[] = array(
                        'taxonomy' => 'service_issue',
                        'field' => 'slug',
                        'terms' => $_GET['service_issue'],
                    );
                }
                
                if (isset($_GET['service_area']) && !empty($_GET['service_area'])) {
                    $tax_query[] = array(
                        'taxonomy' => 'service_area',
                        'field' => 'slug',
                        'terms' => $_GET['service_area'],
                    );
                }
                
                if (count($tax_query) > 1) {
                    $query_args['tax_query'] = $tax_query;
                }
                
                $services_query = new WP_Query($query_args);
                
                if ($services_query->have_posts()):
                ?>
                    
                    <!-- 検索結果の件数表示 -->
                    <div class="service-archive__count">
                        <p><strong><?php echo esc_html($services_query->found_posts); ?>件</strong>のサービスが見つかりました</p>
                    </div>
                    
                    <!-- サービスカード一覧 -->
                    <div class="service-cards">
                        <?php while ($services_query->have_posts()): $services_query->the_post(); ?>
                            <?php if (function_exists('display_service_card')): ?>
                                <?php display_service_card(get_the_ID()); ?>
                            <?php else: ?>
                                <article class="service-card">
                                    <a href="<?php the_permalink(); ?>">
                                        <h3><?php the_title(); ?></h3>
                                    </a>
                                </article>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php if ($services_query->found_posts > 6): ?>
                        <div class="section-footer">
                            <a href="<?php echo esc_url(get_post_type_archive_link('tax_service')); ?>" class="button button--secondary">
                                すべての税理士サービスを見る →
                            </a>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    
                    <!-- 検索結果なし -->
                    <div class="service-archive__no-results">
                        <p class="no-results-text">条件に一致するサービスが見つかりませんでした</p>
                    </div>
                    
                <?php endif; ?>
                
                <?php wp_reset_postdata(); ?>
                
            </div>
        </section>
        
        <!-- 税理士事務所セクション -->
        <section class="service-section tax-office-section">
            <h2 class="section-title">
                <span class="section-title__icon">🏛️</span>
                税理士・会計事務所
            </h2>
            
            <div class="service-archive">
                
                <?php
                // 税理士事務所のクエリ
                $office_query_args = array(
                    'post_type' => 'tax_office',
                    'posts_per_page' => 12,
                    'orderby' => isset($_GET['orderby']) ? $_GET['orderby'] : 'title',
                    'order' => 'ASC',
                );
                
                // 都道府県フィルター
                if (isset($_GET['office_prefecture']) && !empty($_GET['office_prefecture'])) {
                    $office_query_args['tax_query'] = array(
                        array(
                            'taxonomy' => 'office_prefecture',
                            'field' => 'slug',
                            'terms' => $_GET['office_prefecture'],
                        ),
                    );
                }
                
                // 業種や得意分野でのメタクエリ検索
                if (isset($_GET['service_industry']) && !empty($_GET['service_industry'])) {
                    $search_term = get_term_by('slug', $_GET['service_industry'], 'service_industry');
                    if ($search_term) {
                        $office_query_args['meta_query'] = array(
                            'relation' => 'OR',
                            array(
                                'key' => '_tax_office_services',
                                'value' => $search_term->name,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => '_tax_office_industries',
                                'value' => $search_term->name,
                                'compare' => 'LIKE',
                            ),
                        );
                    }
                }
                
                $offices_query = new WP_Query($office_query_args);
                
                if ($offices_query->have_posts()):
                ?>
                    
                    <!-- 検索結果の件数表示 -->
                    <div class="service-archive__count">
                        <p><strong><?php echo esc_html($offices_query->found_posts); ?>件</strong>の事務所が見つかりました</p>
                    </div>
                    
                    <!-- 事務所カード一覧 -->
                    <div class="service-cards">
                        <?php while ($offices_query->have_posts()): $offices_query->the_post(); ?>
                            <article class="service-card">
                                <a href="<?php the_permalink(); ?>" class="service-card__link">
                                    
                                    <div class="service-card__thumbnail">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('service-card'); ?>
                                        <?php else: ?>
                                            🏛️
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="service-card__content">
                                        <h3 class="service-card__title"><?php the_title(); ?></h3>
                                        
                                        <!-- 都道府県 -->
                                        <?php
                                        $prefectures = get_the_terms(get_the_ID(), 'office_prefecture');
                                        if ($prefectures && !is_wp_error($prefectures)):
                                        ?>
                                            <div class="service-card__meta">
                                                <span class="service-card__meta-label">📍</span>
                                                <?php
                                                $prefecture_names = array();
                                                foreach ($prefectures as $prefecture) {
                                                    $prefecture_names[] = esc_html($prefecture->name);
                                                }
                                                echo implode(', ', $prefecture_names);
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- 得意分野（最大3件） -->
                                        <?php
                                        $services = get_post_meta(get_the_ID(), '_tax_office_services', true);
                                        if (!empty($services)):
                                            $services_array = json_decode($services, true);
                                            if (!empty($services_array)):
                                                $display_services = array_slice($services_array, 0, 3);
                                        ?>
                                            <div class="service-card__tags">
                                                <?php foreach ($display_services as $service): ?>
                                                    <span class="service-card__tag"><?php echo esc_html($service); ?></span>
                                                <?php endforeach; ?>
                                                <?php if (count($services_array) > 3): ?>
                                                    <span class="service-card__tag-more">他<?php echo count($services_array) - 3; ?>件</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php 
                                            endif;
                                        endif; 
                                        ?>
                                        
                                        <div class="service-card__footer">
                                            <span class="service-card__link-text">詳細を見る →</span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php if ($offices_query->found_posts > 12): ?>
                        <div class="section-footer">
                            <a href="<?php echo esc_url(get_post_type_archive_link('tax_office')); ?>" class="button button--secondary">
                                すべての税理士事務所を見る →
                            </a>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    
                    <!-- 検索結果なし -->
                    <div class="service-archive__no-results">
                        <p class="no-results-text">条件に一致する事務所が見つかりませんでした</p>
                    </div>
                    
                <?php endif; ?>
                
                <?php wp_reset_postdata(); ?>
                
            </div>
        </section>
        
    </div>
    
</main>

<?php get_footer(); ?>
