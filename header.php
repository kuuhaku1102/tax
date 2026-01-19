<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?php
    // ========================================
    // SEO メタタグ
    // ========================================
    
    // ページタイトル
    $page_title = wp_get_document_title();
    
    // メタディスクリプション
    $meta_description = '';
    if (is_front_page()) {
        $meta_description = 'あなたに最適な税理士を見つける税理士マッチングサイト。業種・課題・エリアから簡単に検索・比較できます。経験豊富な税理士があなたの事業をサポートします。';
    } elseif (is_singular('tax_service')) {
        $excerpt = get_the_excerpt();
        $meta_description = $excerpt ? wp_trim_words($excerpt, 30, '...') : get_bloginfo('description');
    } elseif (is_post_type_archive('tax_service')) {
        $meta_description = '税理士サービスの一覧ページ。業種・課題・エリアから、あなたに最適な税理士を見つけられます。';
    } elseif (is_tax()) {
        $term = get_queried_object();
        $meta_description = $term->description ? wp_trim_words($term->description, 30, '...') : get_bloginfo('description');
    } else {
        $meta_description = get_bloginfo('description');
    }
    ?>
    
    <meta name="description" content="<?php echo esc_attr($meta_description); ?>">
    
    <?php if (is_singular('tax_service')): ?>
        <meta name="keywords" content="税理士,<?php echo esc_attr(get_taxonomy_terms_list('service_industry', get_the_ID())); ?>,<?php echo esc_attr(get_taxonomy_terms_list('service_issue', get_the_ID())); ?>">
    <?php else: ?>
        <meta name="keywords" content="税理士,税理士マッチング,税理士検索,税理士比較,確定申告,法人設立,節税対策">
    <?php endif; ?>
    
    <!-- OGP（Open Graph Protocol） -->
    <meta property="og:type" content="<?php echo is_front_page() ? 'website' : 'article'; ?>">
    <meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($meta_description); ?>">
    <meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
    <?php if (has_post_thumbnail()): ?>
        <meta property="og:image" content="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>">
    <?php else: ?>
        <meta property="og:image" content="<?php echo esc_url(get_template_directory_uri() . '/assets/images/ogp-default.jpg'); ?>">
    <?php endif; ?>
    <meta property="og:locale" content="ja_JP">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($meta_description); ?>">
    <?php if (has_post_thumbnail()): ?>
        <meta name="twitter:image" content="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>">
    <?php endif; ?>
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo esc_url(get_permalink()); ?>">
    
    <!-- Preconnect（パフォーマンス最適化） -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/apple-touch-icon.png'); ?>">
    
    <?php
    // ========================================
    // 構造化データ（JSON-LD）
    // ========================================
    
    // WebSite スキーマ
    if (is_front_page()):
    ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php bloginfo('name'); ?>",
        "url": "<?php echo esc_url(home_url('/')); ?>",
        "description": "<?php echo esc_js($meta_description); ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "<?php echo esc_url(home_url('/services/?s={search_term_string}')); ?>"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <!-- Organization スキーマ -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php bloginfo('name'); ?>",
        "url": "<?php echo esc_url(home_url('/')); ?>",
        "logo": "<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>",
        "sameAs": []
    }
    </script>
    
    <!-- BreadcrumbList スキーマ -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "ホーム",
                "item": "<?php echo esc_url(home_url('/')); ?>"
            }
        ]
    }
    </script>
    <?php endif; ?>
    
    <?php
    // サービス詳細ページの構造化データ
    if (is_singular('tax_service')):
        $service_id = get_the_ID();
        $office_name = get_field_safe('office_name', $service_id, get_the_title());
        $office_url = get_field_safe('office_url', $service_id);
        $phone = get_field_safe('phone', $service_id);
        $address = get_field_safe('address', $service_id);
        $rating = get_field_safe('editor_rating', $service_id, 3);
    ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        "name": "<?php echo esc_js($office_name); ?>",
        "description": "<?php echo esc_js($meta_description); ?>",
        <?php if ($office_url): ?>
        "url": "<?php echo esc_url($office_url); ?>",
        <?php endif; ?>
        <?php if ($phone): ?>
        "telephone": "<?php echo esc_js($phone); ?>",
        <?php endif; ?>
        <?php if ($address): ?>
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "JP",
            "addressLocality": "<?php echo esc_js($address); ?>"
        },
        <?php endif; ?>
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "<?php echo esc_js($rating); ?>",
            "bestRating": "5",
            "worstRating": "1"
        }
    }
    </script>
    <?php endif; ?>
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#main-content" class="skip-link">メインコンテンツへスキップ</a>

<header class="site-header" role="banner">
    <div class="site-header__container">
        <div class="site-header__logo">
            <?php if (is_front_page()): ?>
                <h1 class="site-logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                        <?php bloginfo('name'); ?>
                    </a>
                </h1>
            <?php else: ?>
                <p class="site-logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                        <?php bloginfo('name'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
        
        <nav class="site-nav" role="navigation" aria-label="メインナビゲーション">
            <ul class="site-nav__menu">
                <li class="site-nav__item">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-nav__link">
                        ホーム
                    </a>
                </li>
                <li class="site-nav__item">
                    <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="site-nav__link">
                        税理士を探す
                    </a>
                </li>
                <li class="site-nav__item">
                    <a href="#how-it-works" class="site-nav__link">
                        使い方
                    </a>
                </li>
            </ul>
        </nav>
        
        <button type="button" class="mobile-menu-toggle" aria-label="メニューを開く" aria-expanded="false">
            <span class="mobile-menu-toggle__bar"></span>
            <span class="mobile-menu-toggle__bar"></span>
            <span class="mobile-menu-toggle__bar"></span>
        </button>
    </div>
</header>

<div id="main-content" class="site-content">
