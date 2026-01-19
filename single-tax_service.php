<?php
/**
 * Template Name: 税理士サービス詳細ページ
 * Template Post Type: tax_service
 * 
 * 設計思想:
 * - ACFフィールドの存在チェックを徹底
 * - 表示ロジックをヘルパー関数に委譲
 * - HTMLとPHPを明確に分離
 * 
 * @package TaxMatchingTheme
 */

get_header(); ?>

<main class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        
        <?php
        // 掲載ステータスチェック
        if (!is_service_active(get_the_ID())) {
            echo '<div class="notice notice-warning"><p>このサービスは現在掲載されていません。</p></div>';
            if (!current_user_can('edit_posts')) {
                get_footer();
                exit;
            }
        }
        ?>
        
        <!-- パンくずリスト -->
        <?php display_breadcrumb(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('service-single'); ?>>
            
            <!-- ヘッダー部分 -->
            <header class="service-single__header">
                <h1 class="service-single__title"><?php the_title(); ?></h1>
                
                <?php
                $catchcopy = get_field_safe('catchcopy');
                if ($catchcopy):
                ?>
                    <p class="service-single__catchcopy"><?php echo esc_html($catchcopy); ?></p>
                <?php endif; ?>
                
                <!-- タクソノミー情報 -->
                <div class="service-single__meta">
                    <?php
                    $industries = get_taxonomy_terms_list('service_industry');
                    if ($industries):
                    ?>
                        <div class="service-single__meta-item">
                            <span class="service-single__meta-label">業種:</span>
                            <?php the_taxonomy_terms_links('service_industry'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    $issues = get_taxonomy_terms_list('service_issue');
                    if ($issues):
                    ?>
                        <div class="service-single__meta-item">
                            <span class="service-single__meta-label">課題:</span>
                            <?php the_taxonomy_terms_links('service_issue'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php
                    $areas = get_taxonomy_terms_list('service_area');
                    if ($areas):
                    ?>
                        <div class="service-single__meta-item">
                            <span class="service-single__meta-label">対応エリア:</span>
                            <?php the_taxonomy_terms_links('service_area'); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- 掲載プランバッジ -->
                <?php
                $listing_plan = get_field_safe('listing_plan');
                if ($listing_plan):
                ?>
                    <div class="service-single__badge service-single__badge--<?php echo esc_attr(strtolower($listing_plan)); ?>">
                        <?php echo esc_html(get_listing_plan_label($listing_plan)); ?>
                    </div>
                <?php endif; ?>
            </header>
            
            <!-- アイキャッチ画像 -->
            <?php if (has_post_thumbnail()): ?>
                <div class="service-single__thumbnail">
                    <?php the_post_thumbnail('service-detail'); ?>
                </div>
            <?php endif; ?>
            
            <!-- 本文 -->
            <?php if (get_the_content()): ?>
                <div class="service-single__content">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
            
            <!-- 編集部のおすすめポイント -->
            <?php
            $editor_good_points = get_field_safe('editor_good_points');
            $editor_rating = get_field_safe('editor_rating');
            if ($editor_good_points || $editor_rating):
            ?>
                <section class="service-single__editor-review">
                    <h2 class="service-single__section-title">編集部のおすすめポイント</h2>
                    
                    <?php if ($editor_rating): ?>
                        <div class="service-single__rating">
                            <span class="service-single__rating-label">編集部評価:</span>
                            <span class="service-single__rating-value">
                                <?php echo str_repeat('★', floor($editor_rating)); ?>
                                <?php echo str_repeat('☆', 5 - floor($editor_rating)); ?>
                                (<?php echo esc_html($editor_rating); ?>)
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($editor_good_points): ?>
                        <div class="service-single__editor-content">
                            <?php echo wp_kses_post($editor_good_points); ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            
            <!-- こんな人におすすめ -->
            <?php
            $editor_recommended_for = get_field_safe('editor_recommended_for');
            if ($editor_recommended_for):
            ?>
                <section class="service-single__recommended">
                    <h2 class="service-single__section-title">こんな人におすすめ</h2>
                    <div class="service-single__recommended-content">
                        <?php echo wp_kses_post($editor_recommended_for); ?>
                    </div>
                </section>
            <?php endif; ?>
            
            <!-- サービス詳細情報 -->
            <?php display_service_detail(); ?>
            
            <!-- 相談方法・対応スピード -->
            <?php
            $consultation_method = get_field_safe('consultation_method');
            $response_time = get_field_safe('response_time');
            if ($consultation_method || $response_time):
            ?>
                <section class="service-single__consultation">
                    <h2 class="service-single__section-title">相談について</h2>
                    
                    <?php if ($consultation_method && is_array($consultation_method)): ?>
                        <div class="service-single__consultation-method">
                            <h3>対応可能な相談方法</h3>
                            <ul>
                                <?php 
                                $method_labels = [
                                    'face_to_face' => '対面',
                                    'online' => 'オンライン',
                                    'phone' => '電話',
                                    'email' => 'メール',
                                    'chat' => 'チャット',
                                ];
                                foreach ($consultation_method as $method): 
                                    $label = isset($method_labels[$method]) ? $method_labels[$method] : $method;
                                ?>
                                    <li><?php echo esc_html($label); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($response_time): ?>
                        <div class="service-single__response-time">
                            <h3>対応スピード</h3>
                            <p><?php 
                            $time_labels = [
                                'within_24hours' => '24時間以内',
                                'within_3days' => '3営業日以内',
                                'within_1week' => '1週間以内',
                                'within_2weeks' => '2週間以内',
                            ];
                            $label = isset($time_labels[$response_time]) ? $time_labels[$response_time] : $response_time;
                            echo esc_html($label); 
                            ?></p>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            
            <!-- お問い合わせボタン -->
            <?php
            $office_url = get_field_safe('office_url');
            $office_tel = get_field_safe('office_tel');
            $office_email = get_field_safe('office_email');
            if ($office_url || $office_tel || $office_email):
            ?>
                <section class="service-single__contact">
                    <h2 class="service-single__section-title">お問い合わせ</h2>
                    <div class="service-single__contact-buttons">
                        <?php if ($office_url): ?>
                            <a href="<?php echo esc_url($office_url); ?>" class="service-single__contact-button service-single__contact-button--website" target="_blank" rel="noopener">
                                公式サイトを見る
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($office_tel): ?>
                            <a href="tel:<?php echo esc_attr(str_replace(['-', ' '], '', $office_tel)); ?>" class="service-single__contact-button service-single__contact-button--tel">
                                電話で問い合わせる
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($office_email): ?>
                            <a href="mailto:<?php echo esc_attr($office_email); ?>" class="service-single__contact-button service-single__contact-button--email">
                                メールで問い合わせる
                            </a>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
            
        </article>
        
        <!-- 関連サービス -->
        <?php
        $related_services = get_related_services(get_the_ID(), 5);
        if ($related_services->have_posts()):
        ?>
            <section class="service-single__related">
                <h2 class="service-single__section-title">関連する税理士サービス</h2>
                <div class="service-cards">
                    <?php while ($related_services->have_posts()): $related_services->the_post(); ?>
                        <?php display_service_card(get_the_ID()); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </section>
        <?php endif; ?>
        
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
