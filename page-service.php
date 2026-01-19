<?php
/**
 * Template Name: サービス別ページ
 * 
 * 設計思想:
 * - 各サービス（顧問税理士、確定申告など）の専用ページ
 * - SEO対策を重視
 * - 見積もり依頼への導線を強化
 * 
 * @package TaxMatchingTheme
 */

get_header();
?>

<div class="service-page">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- ヒーローセクション -->
        <section class="service-hero">
            <div class="container">
                <div class="hero-content">
                    <h1 class="hero-title"><?php the_title(); ?></h1>
                    <?php if (has_excerpt()) : ?>
                        <p class="hero-description"><?php the_excerpt(); ?></p>
                    <?php endif; ?>
                    <div class="hero-cta">
                        <a href="<?php echo home_url('/estimate/'); ?>" class="btn btn-primary btn-large">
                            無料で見積もりを依頼する
                        </a>
                    </div>
                </div>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="hero-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- サービス内容 -->
        <section class="service-content">
            <div class="container">
                <div class="content-wrapper">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>

        <!-- このサービスが選ばれる理由 -->
        <?php if (get_field('reasons')) : ?>
            <section class="service-reasons">
                <div class="container">
                    <h2 class="section-title">このサービスが選ばれる理由</h2>
                    <div class="reasons-grid">
                        <?php 
                        $reasons = get_field('reasons');
                        if (is_array($reasons)) :
                            foreach ($reasons as $index => $reason) : ?>
                                <div class="reason-card">
                                    <div class="reason-number"><?php echo sprintf('%02d', $index + 1); ?></div>
                                    <h3 class="reason-title"><?php echo esc_html($reason['title']); ?></h3>
                                    <p class="reason-description"><?php echo esc_html($reason['description']); ?></p>
                                </div>
                            <?php endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- 料金の目安 -->
        <?php if (get_field('pricing_guide')) : ?>
            <section class="service-pricing">
                <div class="container">
                    <h2 class="section-title">料金の目安</h2>
                    <div class="pricing-content">
                        <?php echo wpautop(get_field('pricing_guide')); ?>
                    </div>
                    <p class="pricing-note">
                        ※ 料金は目安です。詳細はお見積もりをご依頼ください。
                    </p>
                </div>
            </section>
        <?php endif; ?>

        <!-- ご利用の流れ -->
        <section class="service-flow">
            <div class="container">
                <h2 class="section-title">ご利用の流れ</h2>
                <div class="flow-steps">
                    <div class="flow-step">
                        <div class="step-number">1</div>
                        <h3 class="step-title">お見積もり依頼</h3>
                        <p class="step-description">フォームからご依頼内容を送信</p>
                    </div>
                    <div class="flow-arrow">→</div>
                    <div class="flow-step">
                        <div class="step-number">2</div>
                        <h3 class="step-title">見積もり受領</h3>
                        <p class="step-description">最適な税理士から見積もりが届く</p>
                    </div>
                    <div class="flow-arrow">→</div>
                    <div class="flow-step">
                        <div class="step-number">3</div>
                        <h3 class="step-title">税理士へご依頼</h3>
                        <p class="step-description">見積もりを比較してご発注</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- よくある質問 -->
        <?php if (get_field('faqs')) : ?>
            <section class="service-faq">
                <div class="container">
                    <h2 class="section-title">よくある質問</h2>
                    <div class="faq-list">
                        <?php 
                        $faqs = get_field('faqs');
                        if (is_array($faqs)) :
                            foreach ($faqs as $faq) : ?>
                                <div class="faq-item">
                                    <h3 class="faq-question">Q. <?php echo esc_html($faq['question']); ?></h3>
                                    <div class="faq-answer">
                                        <p>A. <?php echo esc_html($faq['answer']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- 最終CTA -->
        <section class="service-final-cta">
            <div class="container">
                <div class="cta-box">
                    <h2 class="cta-title">まずは無料でお見積もり</h2>
                    <p class="cta-description">
                        完全無料で、あなたに最適な税理士をご紹介します。<br>
                        まずはお気軽にお見積もりをご依頼ください。
                    </p>
                    <a href="<?php echo home_url('/estimate/'); ?>" class="btn btn-primary btn-large">
                        無料で見積もりを依頼する
                    </a>
                </div>
            </div>
        </section>

    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
