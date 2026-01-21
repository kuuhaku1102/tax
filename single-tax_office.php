<?php
/**
 * Template Name: 税理士事務所詳細ページ
 * Template Post Type: tax_office
 * 
 * @package TaxMatchingTheme
 */

get_header(); ?>

<main class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- パンくずリスト -->
        <?php if (function_exists('display_breadcrumb')) display_breadcrumb(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('tax-office-single'); ?>>
            
            <!-- ヘッダー部分 -->
            <header class="tax-office-single__header">
                <h1 class="tax-office-single__title"><?php the_title(); ?></h1>
                
                <!-- 都道府県情報 -->
                <div class="tax-office-single__meta">
                    <?php
                    $prefectures = get_the_terms(get_the_ID(), 'office_prefecture');
                    if ($prefectures && !is_wp_error($prefectures)):
                    ?>
                        <div class="tax-office-single__meta-item">
                            <span class="tax-office-single__meta-label">所在都道府県:</span>
                            <?php
                            $prefecture_names = array();
                            foreach ($prefectures as $prefecture) {
                                $prefecture_names[] = '<a href="' . esc_url(get_term_link($prefecture)) . '">' . esc_html($prefecture->name) . '</a>';
                            }
                            echo implode(', ', $prefecture_names);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </header>
            
            <!-- アイキャッチ画像 -->
            <?php if (has_post_thumbnail()): ?>
                <div class="tax-office-single__thumbnail">
                    <?php the_post_thumbnail('service-detail'); ?>
                </div>
            <?php endif; ?>
            
            <!-- 本文 -->
            <?php if (get_the_content()): ?>
                <div class="tax-office-single__content">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
            
            <!-- 得意分野 -->
            <?php
            $services = get_post_meta(get_the_ID(), '_tax_office_services', true);
            if (!empty($services)):
                $services_array = json_decode($services, true);
                if (!empty($services_array)):
            ?>
                <section class="tax-office-single__services">
                    <h2 class="tax-office-single__section-title">得意分野</h2>
                    <ul class="tax-office-single__list">
                        <?php foreach ($services_array as $service): ?>
                            <li class="tax-office-single__list-item">
                                <span class="tax-office-single__tag"><?php echo esc_html($service); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php 
                endif;
            endif; 
            ?>
            
            <!-- 得意業種 -->
            <?php
            $industries = get_post_meta(get_the_ID(), '_tax_office_industries', true);
            if (!empty($industries)):
                $industries_array = json_decode($industries, true);
                if (!empty($industries_array)):
            ?>
                <section class="tax-office-single__industries">
                    <h2 class="tax-office-single__section-title">得意業種</h2>
                    <ul class="tax-office-single__list">
                        <?php foreach ($industries_array as $industry): ?>
                            <li class="tax-office-single__list-item">
                                <span class="tax-office-single__tag"><?php echo esc_html($industry); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php 
                endif;
            endif; 
            ?>
            
            <!-- 事務所情報 -->
            <?php
            $address = get_post_meta(get_the_ID(), '_tax_office_address', true);
            $phone = get_post_meta(get_the_ID(), '_tax_office_phone', true);
            $email = get_post_meta(get_the_ID(), '_tax_office_email', true);
            $website = get_post_meta(get_the_ID(), '_tax_office_website', true);
            
            if ($address || $phone || $email || $website):
            ?>
                <section class="tax-office-single__info">
                    <h2 class="tax-office-single__section-title">事務所情報</h2>
                    <table class="tax-office-single__info-table">
                        <?php if ($address): ?>
                            <tr>
                                <th>所在地</th>
                                <td><?php echo esc_html($address); ?></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($phone): ?>
                            <tr>
                                <th>電話番号</th>
                                <td><a href="tel:<?php echo esc_attr(str_replace(['-', ' '], '', $phone)); ?>"><?php echo esc_html($phone); ?></a></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($email): ?>
                            <tr>
                                <th>メールアドレス</th>
                                <td><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if ($website): ?>
                            <tr>
                                <th>ウェブサイト</th>
                                <td><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener"><?php echo esc_html($website); ?></a></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </section>
            <?php endif; ?>
            
            <!-- お問い合わせボタン -->
            <?php if ($website || $phone || $email): ?>
                <section class="tax-office-single__contact">
                    <h2 class="tax-office-single__section-title">お問い合わせ</h2>
                    <div class="tax-office-single__contact-buttons">
                        <?php if ($website): ?>
                            <a href="<?php echo esc_url($website); ?>" class="tax-office-single__contact-button tax-office-single__contact-button--website" target="_blank" rel="noopener">
                                公式サイトを見る
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($phone): ?>
                            <a href="tel:<?php echo esc_attr(str_replace(['-', ' '], '', $phone)); ?>" class="tax-office-single__contact-button tax-office-single__contact-button--tel">
                                電話で問い合わせる
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($email): ?>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="tax-office-single__contact-button tax-office-single__contact-button--email">
                                メールで問い合わせる
                            </a>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
            
        </article>
        
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
