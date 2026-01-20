<?php
/**
 * Template Name: フロントページ（トップページ）
 * 
 * SEO対策:
 * - セマンティックHTML5タグの使用
 * - 構造化データ（JSON-LD）の実装
 * - 適切な見出し階層（H1-H6）
 * - 内部リンクの最適化
 * - ページ表示速度の最適化
 * - モバイルファーストデザイン
 * 
 * @package TaxMatchingTheme
 */

get_header(); ?>

<main class="front-page" role="main">
    
    <!-- ========================================
         ヒーローセクション
         SEO: H1タグ、キーワード配置、CTA
         ======================================== -->
    <section class="hero" itemscope itemtype="https://schema.org/WebPageElement">
        <div class="hero__container">
            <h1 class="hero__title" itemprop="name">
                あなたに最適な税理士を見つける<br>
                税理士マッチングサイト
            </h1>
            <p class="hero__description" itemprop="description">
                業種・課題・エリアから、あなたのビジネスに最適な税理士サービスを簡単に検索・比較できます。
                経験豊富な税理士があなたの事業をサポートします。
            </p>
            <div class="hero__cta">
                <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="hero__button hero__button--primary" rel="nofollow">
                    税理士を探す
                </a>
                <a href="#how-it-works" class="hero__button hero__button--secondary">
                    使い方を見る
                </a>
            </div>
            
            <!-- 信頼性の証明 -->
            <div class="hero__trust-signals">
                <div class="trust-signal">
                    <span class="trust-signal__number"><?php echo esc_html(get_total_services_count()); ?></span>
                    <span class="trust-signal__label">掲載サービス数</span>
                </div>
                <div class="trust-signal">
                    <span class="trust-signal__number">全国</span>
                    <span class="trust-signal__label">対応エリア</span>
                </div>
                <div class="trust-signal">
                    <span class="trust-signal__number">無料</span>
                    <span class="trust-signal__label">相談・比較</span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         注目の税理士サービス
         SEO: 内部リンク、キーワード配置
         ======================================== -->
    <?php
    $featured_services = get_featured_services(6);
    if ($featured_services->have_posts()):
    ?>
    <section class="featured-services" itemscope itemtype="https://schema.org/ItemList">
        <div class="section-container">
            <header class="section-header">
                <h2 class="section-header__title" itemprop="name">注目の税理士サービス</h2>
                <p class="section-header__description">
                    編集部が厳選した、おすすめの税理士サービスをご紹介します
                </p>
            </header>
            
            <div class="service-cards" itemprop="itemListElement">
                <?php 
                $position = 1;
                while ($featured_services->have_posts()): 
                    $featured_services->the_post(); 
                    display_service_card(get_the_ID(), $position);
                    $position++;
                endwhile; 
                wp_reset_postdata();
                ?>
            </div>
            
            <div class="section-footer">
                <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="section-footer__link">
                    すべての税理士サービスを見る →
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- ========================================
         業種から探す
         SEO: カテゴリーリンク、キーワード配置
         ======================================== -->
    <?php
    $industries = get_terms([
        'taxonomy' => 'service_industry',
        'hide_empty' => true,
        'number' => 8,
        'orderby' => 'count',
        'order' => 'DESC',
    ]);
    
    if (!empty($industries) && !is_wp_error($industries)):
    ?>
    <section class="search-by-industry" itemscope itemtype="https://schema.org/ItemList">
        <div class="section-container">
            <header class="section-header">
                <h2 class="section-header__title" itemprop="name">業種から税理士を探す</h2>
                <p class="section-header__description">
                    あなたの業種に特化した税理士サービスを見つけましょう
                </p>
            </header>
            
            <nav class="industry-grid" aria-label="業種別ナビゲーション">
                <?php foreach ($industries as $index => $industry): ?>
                    <a href="<?php echo esc_url(get_term_link($industry)); ?>" 
                       class="industry-card" 
                       itemprop="itemListElement" 
                       itemscope 
                       itemtype="https://schema.org/ListItem">
                        <meta itemprop="position" content="<?php echo esc_attr($index + 1); ?>">
                        <h3 class="industry-card__title" itemprop="name">
                            <?php echo esc_html($industry->name); ?>
                        </h3>
                        <p class="industry-card__count">
                            <?php echo esc_html($industry->count); ?>件のサービス
                        </p>
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <div class="section-footer">
                <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="section-footer__link">
                    すべての業種を見る →
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- ========================================
         課題から探す
         SEO: 問題解決キーワード、内部リンク
         ======================================== -->
    <?php
    $issues = get_terms([
        'taxonomy' => 'service_issue',
        'hide_empty' => true,
        'number' => 6,
        'orderby' => 'count',
        'order' => 'DESC',
    ]);
    
    if (!empty($issues) && !is_wp_error($issues)):
    ?>
    <section class="search-by-issue">
        <div class="section-container">
            <header class="section-header">
                <h2 class="section-header__title">課題から税理士を探す</h2>
                <p class="section-header__description">
                    あなたのビジネス課題を解決できる税理士サービスを見つけましょう
                </p>
            </header>
            
            <nav class="issue-grid" aria-label="課題別ナビゲーション">
                <?php foreach ($issues as $issue): ?>
                    <a href="<?php echo esc_url(get_term_link($issue)); ?>" class="issue-card">
                        <h3 class="issue-card__title">
                            <?php echo esc_html($issue->name); ?>
                        </h3>
                        <p class="issue-card__count">
                            <?php echo esc_html($issue->count); ?>件のサービス
                        </p>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- ========================================
         使い方（How It Works）
         SEO: 手順の明確化、ユーザビリティ向上
         ======================================== -->
    <section id="how-it-works" class="how-it-works" itemscope itemtype="https://schema.org/HowTo">
        <div class="section-container">
            <header class="section-header">
                <h2 class="section-header__title" itemprop="name">税理士マッチングサイトの使い方</h2>
                <p class="section-header__description" itemprop="description">
                    簡単3ステップで、あなたに最適な税理士を見つけられます
                </p>
            </header>
            
            <div class="steps">
                <article class="step" itemprop="step" itemscope itemtype="https://schema.org/HowToStep">
                    <meta itemprop="position" content="1">
                    <div class="step__number" aria-hidden="true">1</div>
                    <h3 class="step__title" itemprop="name">条件を選んで検索</h3>
                    <p class="step__description" itemprop="text">
                        業種・課題・エリアなど、あなたの条件に合った税理士サービスを検索します。
                        複数の条件を組み合わせて、より精度の高い検索が可能です。
                    </p>
                </article>
                
                <article class="step" itemprop="step" itemscope itemtype="https://schema.org/HowToStep">
                    <meta itemprop="position" content="2">
                    <div class="step__number" aria-hidden="true">2</div>
                    <h3 class="step__title" itemprop="name">サービスを比較</h3>
                    <p class="step__description" itemprop="text">
                        検索結果から気になる税理士サービスを比較します。
                        料金体系、対応スピード、編集部評価などを確認して、最適なサービスを選びましょう。
                    </p>
                </article>
                
                <article class="step" itemprop="step" itemscope itemtype="https://schema.org/HowToStep">
                    <meta itemprop="position" content="3">
                    <div class="step__number" aria-hidden="true">3</div>
                    <h3 class="step__title" itemprop="name">無料で相談</h3>
                    <p class="step__description" itemprop="text">
                        気になる税理士サービスが見つかったら、無料で相談できます。
                        オンライン・対面・電話など、あなたの都合に合わせた相談方法を選べます。
                    </p>
                </article>
            </div>
            
            <div class="section-footer">
                <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="cta-button">
                    今すぐ税理士を探す
                </a>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         このサイトの特徴
         SEO: 独自性の強調、信頼性の向上
         ======================================== -->
    <section class="features">
        <div class="section-container">
            <header class="section-header">
                <h2 class="section-header__title">このサイトの特徴</h2>
                <p class="section-header__description">
                    税理士マッチングサイトが選ばれる理由
                </p>
            </header>
            
            <div class="feature-grid">
                <article class="feature">
                    <h3 class="feature__title">完全無料</h3>
                    <p class="feature__description">
                        サービスの検索・比較・相談まで、すべて無料でご利用いただけます。
                        安心してあなたに最適な税理士を見つけられます。
                    </p>
                </article>
                
                <article class="feature">
                    <h3 class="feature__title">厳選されたサービス</h3>
                    <p class="feature__description">
                        編集部が厳選した、信頼できる税理士サービスのみを掲載しています。
                        実績・評判・対応力を基準に選定しています。
                    </p>
                </article>
                
                <article class="feature">
                    <h3 class="feature__title">詳細な情報</h3>
                    <p class="feature__description">
                        料金体系、対応業種、相談方法など、詳細な情報を掲載しています。
                        比較検討に必要な情報がすべて揃っています。
                    </p>
                </article>
                
                <article class="feature">
                    <h3 class="feature__title">全国対応</h3>
                    <p class="feature__description">
                        全国各地の税理士サービスを掲載しています。
                        オンライン対応のサービスも多数掲載しており、場所を問わず利用できます。
                    </p>
                </article>
            </div>
        </div>
    </section>
    
    <!-- ========================================
         よくある質問（FAQ）
         SEO: FAQスキーマ、ロングテールキーワード対策
         ======================================== -->
    <section class="faq" itemscope itemtype="https://schema.org/FAQPage">
        <div class="section-container">
            <header class="section-header">
                <h2 class="section-header__title">よくある質問</h2>
            </header>
            
            <dl class="faq-list">
                <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <dt class="faq-item__question" itemprop="name">
                        <button type="button" class="faq-item__toggle" aria-expanded="false">
                            税理士マッチングサイトは無料で利用できますか？
                        </button>
                    </dt>
                    <dd class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <div itemprop="text">
                            <p>はい、完全無料でご利用いただけます。サービスの検索・比較・相談まで、すべて無料です。税理士サービスとの契約時に発生する費用は、各サービスの料金体系をご確認ください。</p>
                        </div>
                    </dd>
                </div>
                
                <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <dt class="faq-item__question" itemprop="name">
                        <button type="button" class="faq-item__toggle" aria-expanded="false">
                            どのような税理士サービスが掲載されていますか？
                        </button>
                    </dt>
                    <dd class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <div itemprop="text">
                            <p>個人事業主向けから法人向けまで、幅広い税理士サービスを掲載しています。確定申告、法人設立、節税対策、相続税対策など、様々な課題に対応できるサービスを見つけられます。</p>
                        </div>
                    </dd>
                </div>
                
                <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <dt class="faq-item__question" itemprop="name">
                        <button type="button" class="faq-item__toggle" aria-expanded="false">
                            相談はどのように行いますか？
                        </button>
                    </dt>
                    <dd class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <div itemprop="text">
                            <p>各税理士サービスの詳細ページから、直接お問い合わせいただけます。対面・オンライン・電話・メールなど、あなたの都合に合わせた相談方法を選べます。</p>
                        </div>
                    </dd>
                </div>
                
                <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <dt class="faq-item__question" itemprop="name">
                        <button type="button" class="faq-item__toggle" aria-expanded="false">
                            複数の税理士サービスに相談できますか？
                        </button>
                    </dt>
                    <dd class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <div itemprop="text">
                            <p>はい、複数のサービスに相談して比較検討することをおすすめします。料金体系や対応内容を比較することで、あなたに最適な税理士を見つけられます。</p>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </section>
    
    <!-- ========================================
         CTA（Call to Action）
         SEO: コンバージョン最適化
         ======================================== -->
    <section class="final-cta">
        <div class="section-container">
            <h2 class="final-cta__title">あなたに最適な税理士を見つけましょう</h2>
            <p class="final-cta__description">
                今すぐ無料で検索・比較・相談できます
            </p>
            <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="final-cta__button">
                税理士を探す
            </a>
        </div>
    </section>
    
</main>

<?php get_footer(); ?>
