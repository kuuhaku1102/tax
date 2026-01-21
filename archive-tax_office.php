<?php
/**
 * Template Name: 税理士事務所一覧ページ
 * Template Post Type: tax_office
 * 
 * @package TaxMatchingTheme
 */

get_header(); ?>

<main class="site-main archive-page tax-office-archive">
    
    <!-- パンくずリスト -->
    <?php if (function_exists('display_breadcrumb')) display_breadcrumb(); ?>
    
    <!-- ページヘッダー -->
    <header class="archive-header">
        <div class="container">
            <h1 class="archive-header__title">税理士・会計事務所を探す</h1>
            <p class="archive-header__description">
                全国の税理士・会計事務所から、あなたに最適な事務所を見つけましょう
            </p>
        </div>
    </header>
    
    <div class="container">
        
        <!-- 検索・フィルターフォーム -->
        <div class="service-filter">
            <form method="get" action="<?php echo esc_url(get_post_type_archive_link('tax_office')); ?>" class="service-filter__form">
                
                <div class="service-filter__grid">
                    <!-- 都道府県フィルター -->
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
                            <option value="title" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'title'); ?>>名前順</option>
                            <option value="date" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'date'); ?>>新着順</option>
                        </select>
                    </div>
                </div>
                
                <div class="service-filter__actions">
                    <button type="submit" class="button button--primary">
                        <span class="button__icon">🔍</span>
                        検索する
                    </button>
                    <a href="<?php echo esc_url(get_post_type_archive_link('tax_office')); ?>" class="button button--secondary">
                        条件をクリア
                    </a>
                </div>
                
            </form>
        </div>
        
        <!-- 事務所一覧 -->
        <div class="service-archive">
            
            <?php
            // 検索クエリの構築
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query_args = array(
                'post_type' => 'tax_office',
                'posts_per_page' => 20,
                'paged' => $paged,
                'orderby' => isset($_GET['orderby']) ? $_GET['orderby'] : 'title',
                'order' => 'ASC',
            );
            
            // 都道府県フィルター
            if (isset($_GET['office_prefecture']) && !empty($_GET['office_prefecture'])) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'office_prefecture',
                        'field' => 'slug',
                        'terms' => $_GET['office_prefecture'],
                    ),
                );
            }
            
            $offices_query = new WP_Query($query_args);
            
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
                                    <h2 class="service-card__title"><?php the_title(); ?></h2>
                                    
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
                
                <!-- ページネーション -->
                <?php
                if ($offices_query->max_num_pages > 1):
                    $big = 999999999;
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, $paged),
                        'total' => $offices_query->max_num_pages,
                        'prev_text' => '← 前へ',
                        'next_text' => '次へ →',
                    ));
                    echo '</div>';
                endif;
                ?>
                
            <?php else: ?>
                
                <!-- 検索結果なし -->
                <div class="service-archive__no-results">
                    <div class="no-results-icon">🔍</div>
                    <h2 class="no-results-title">条件に一致する事務所が見つかりませんでした</h2>
                    <p class="no-results-text">検索条件を変更して、もう一度お試しください。</p>
                    <a href="<?php echo esc_url(get_post_type_archive_link('tax_office')); ?>" class="button button--primary">
                        すべての事務所を見る
                    </a>
                </div>
                
            <?php endif; ?>
            
            <?php wp_reset_postdata(); ?>
            
        </div>
        
    </div>
    
</main>

<?php get_footer(); ?>
