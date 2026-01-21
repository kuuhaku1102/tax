<?php
/**
 * Template Name: サービスページ（統合版）
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
                得意業種・得意分野・都道府県から、あなたに最適な税理士サービス・事務所を見つけましょう
            </p>
        </div>
    </header>
    
    <div class="container">
        
        <!-- 検索・フィルターフォーム -->
        <div class="service-filter">
            <form method="get" action="<?php echo esc_url(get_permalink()); ?>" class="service-filter__form">
                
                <div class="service-filter__grid">
                    
                    <!-- 得意業種フィルター -->
                    <div class="service-filter__field">
                        <label class="service-filter__label">得意業種</label>
                        <select name="office_industry" class="service-filter__select">
                            <option value="">すべて</option>
                            <?php
                            if (function_exists('get_all_office_industries')):
                                $industries = get_all_office_industries();
                                foreach ($industries as $industry):
                                    $selected = isset($_GET['office_industry']) && $_GET['office_industry'] == $industry ? 'selected' : '';
                            ?>
                                    <option value="<?php echo esc_attr($industry); ?>" <?php echo $selected; ?>>
                                        <?php echo esc_html($industry); ?>
                                    </option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    
                    <!-- 得意分野フィルター -->
                    <div class="service-filter__field">
                        <label class="service-filter__label">得意分野</label>
                        <select name="office_service" class="service-filter__select">
                            <option value="">すべて</option>
                            <?php
                            if (function_exists('get_all_office_services')):
                                $services = get_all_office_services();
                                foreach ($services as $service):
                                    $selected = isset($_GET['office_service']) && $_GET['office_service'] == $service ? 'selected' : '';
                            ?>
                                    <option value="<?php echo esc_attr($service); ?>" <?php echo $selected; ?>>
                                        <?php echo esc_html($service); ?>
                                    </option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    
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
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="button button--secondary">
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
                // 税理士サービスのクエリ
                $service_query_args = array(
                    'post_type' => 'tax_service',
                    'posts_per_page' => 6,
                    'orderby' => isset($_GET['orderby']) ? $_GET['orderby'] : 'date',
                    'order' => 'DESC',
                );
                
                $services_query = new WP_Query($service_query_args);
                
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
                // アーカイブページではないため、$_GETからpagedを取得
                $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
                $office_query_args = array(
                    'post_type' => 'tax_office',
                    'posts_per_page' => 24,
                    'paged' => $paged,
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
                
                // 得意業種・得意分野でのメタクエリ検索
                $meta_query = array('relation' => 'AND');
                
                // 得意業種で絞り込み
                if (isset($_GET['office_industry']) && !empty($_GET['office_industry'])) {
                    $meta_query[] = array(
                        'key' => '_tax_office_industries',
                        'value' => '"' . $_GET['office_industry'] . '"',
                        'compare' => 'LIKE',
                    );
                }
                
                // 得意分野で絞り込み
                if (isset($_GET['office_service']) && !empty($_GET['office_service'])) {
                    $meta_query[] = array(
                        'key' => '_tax_office_services',
                        'value' => '"' . $_GET['office_service'] . '"',
                        'compare' => 'LIKE',
                    );
                }
                
                if (count($meta_query) > 1) {
                    $office_query_args['meta_query'] = $meta_query;
                }
                
                $offices_query = new WP_Query($office_query_args);
                
                if ($offices_query->have_posts()):
                ?>
                    
                    <!-- 検索結果の件数表示 -->
                    <div class="service-archive__count">
                        <p><strong><?php echo esc_html($offices_query->found_posts); ?>件</strong>の事務所が見つかりました</p>
                    </div>
                    
                    <!-- 事務所カード一覧 -->
                    <div class="office-cards">
                        <?php while ($offices_query->have_posts()): $offices_query->the_post(); ?>
                            <article class="office-card">
                                <a href="<?php the_permalink(); ?>" class="office-card__link">
                                    
                                    <div class="office-card__thumbnail">
                                        <?php if (has_post_thumbnail()): ?>
                                            <?php the_post_thumbnail('service-card'); ?>
                                        <?php else: ?>
                                            🏛️
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="office-card__content">
                                        <h3 class="office-card__title"><?php the_title(); ?></h3>
                                        
                                        <!-- 都道府県 -->
                                        <?php
                                        $prefectures = get_the_terms(get_the_ID(), 'office_prefecture');
                                        if ($prefectures && !is_wp_error($prefectures)):
                                        ?>
                                            <div class="office-card__meta">
                                                <span class="office-card__meta-label">📍</span>
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
                                            <div class="office-card__tags">
                                                <?php foreach ($display_services as $service): ?>
                                                    <span class="office-card__tag"><?php echo esc_html($service); ?></span>
                                                <?php endforeach; ?>
                                                <?php if (count($services_array) > 3): ?>
                                                    <span class="office-card__tag-more">他<?php echo count($services_array) - 3; ?>件</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php 
                                            endif;
                                        endif; 
                                        ?>
                                        
                                        <div class="office-card__footer">
                                            <span class="office-card__link-text">詳細を見る →</span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- ページネーション -->
                    <?php if ($offices_query->max_num_pages > 1): ?>
                        <div class="pagination">
                            <?php
                            $pagination_args = array(
                                'total' => $offices_query->max_num_pages,
                                'current' => $paged,
                                'format' => '?paged=%#%',
                                'prev_text' => '« 前へ',
                                'next_text' => '次へ »',
                                'add_args' => array(),
                            );
                            
                            // 検索パラメータを保持
                            if (isset($_GET['office_industry'])) {
                                $pagination_args['add_args']['office_industry'] = $_GET['office_industry'];
                            }
                            if (isset($_GET['office_service'])) {
                                $pagination_args['add_args']['office_service'] = $_GET['office_service'];
                            }
                            if (isset($_GET['office_prefecture'])) {
                                $pagination_args['add_args']['office_prefecture'] = $_GET['office_prefecture'];
                            }
                            if (isset($_GET['orderby'])) {
                                $pagination_args['add_args']['orderby'] = $_GET['orderby'];
                            }
                            
                            echo paginate_links($pagination_args);
                            ?>
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
