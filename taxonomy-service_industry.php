<?php
/**
 * Template Name: 業種別税理士サービス一覧
 * Taxonomy: service_industry
 * 
 * 設計思想:
 * - archive-tax_service.php と同様の構造
 * - タクソノミー情報を表示
 * 
 * @package TaxMatchingTheme
 */

get_header();

$term = get_queried_object();
?>

<main class="site-main">
    
    <!-- パンくずリスト -->
    <?php display_breadcrumb(); ?>
    
    <!-- ページヘッダー -->
    <header class="archive-header">
        <h1 class="archive-header__title">
            <?php echo esc_html($term->name); ?>の税理士サービス
        </h1>
        <?php if ($term->description): ?>
            <div class="archive-header__description">
                <?php echo wp_kses_post($term->description); ?>
            </div>
        <?php endif; ?>
    </header>
    
    <!-- サービス一覧 -->
    <div class="service-archive">
        
        <?php if (have_posts()): ?>
            
            <!-- 検索結果の件数表示 -->
            <div class="service-archive__count">
                <p><?php echo esc_html($wp_query->found_posts); ?>件のサービスが見つかりました</p>
            </div>
            
            <!-- サービスカード一覧 -->
            <div class="service-cards">
                <?php while (have_posts()): the_post(); ?>
                    <?php display_service_card(get_the_ID()); ?>
                <?php endwhile; ?>
            </div>
            
            <!-- ページネーション -->
            <?php display_pagination(); ?>
            
        <?php else: ?>
            
            <!-- 検索結果なし -->
            <div class="service-archive__no-results">
                <p>この業種に対応する税理士サービスが見つかりませんでした。</p>
                <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="button">
                    すべてのサービスを見る
                </a>
            </div>
            
        <?php endif; ?>
        
    </div>
    
</main>

<?php get_footer(); ?>
