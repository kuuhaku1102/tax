<?php
/**
 * Template Name: 税理士サービス一覧ページ
 * Template Post Type: tax_service
 * 
 * 設計思想:
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
    <?php display_breadcrumb(); ?>
    
    <!-- ページヘッダー -->
    <header class="archive-header">
        <div class="container">
            <h1 class="archive-header__title">税理士サービスを探す</h1>
            <p class="archive-header__description">
                業種・課題・エリアから、あなたに最適な税理士サービスを見つけましょう
            </p>
        </div>
    </header>
    
    <div class="container">
        
        <!-- 検索・フィルターフォーム -->
        <div class="service-filter">
            <form method="get" action="<?php echo esc_url(get_services_archive_url()); ?>" class="service-filter__form">
                
                <div class="service-filter__grid">
                    <?php display_taxonomy_filter('service_industry', '業種'); ?>
                    <?php display_taxonomy_filter('service_issue', '課題・目的'); ?>
                    <?php display_taxonomy_filter('service_area', '対応エリア'); ?>
                    
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
                    <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="button button--secondary">
                        条件をクリア
                    </a>
                </div>
                
            </form>
        </div>
        
        <!-- サービス一覧 -->
        <div class="service-archive">
            
            <?php
            // 検索クエリの構築
            $query_args = build_search_query_from_params();
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
                        <?php display_service_card(get_the_ID()); ?>
                    <?php endwhile; ?>
                </div>
                
                <!-- ページネーション -->
                <?php display_pagination($services_query); ?>
                
            <?php else: ?>
                
                <!-- 検索結果なし -->
                <div class="service-archive__no-results">
                    <div class="no-results-icon">🔍</div>
                    <h2 class="no-results-title">条件に一致するサービスが見つかりませんでした</h2>
                    <p class="no-results-text">検索条件を変更して、もう一度お試しください。</p>
                    <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="button button--primary">
                        すべてのサービスを見る
                    </a>
                </div>
                
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
            
        </div>
        
        <!-- 注目サービス -->
        <?php
        $featured_services = get_featured_services(5);
        if ($featured_services->have_posts() && !$services_query->have_posts()):
        ?>
            <section class="service-archive__featured">
                <h2 class="section-title">注目の税理士サービス</h2>
                <div class="service-cards">
                    <?php while ($featured_services->have_posts()): $featured_services->the_post(); ?>
                        <?php display_service_card(get_the_ID()); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>
        
    </div>
    
</main>

<?php get_footer(); ?>
