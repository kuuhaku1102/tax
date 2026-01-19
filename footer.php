</div><!-- .site-content -->

<footer class="site-footer" role="contentinfo">
    <div class="site-footer__container">
        
        <!-- フッターナビゲーション -->
        <nav class="footer-nav" aria-label="フッターナビゲーション">
            <div class="footer-nav__section">
                <h3 class="footer-nav__title">サービス</h3>
                <ul class="footer-nav__menu">
                    <li class="footer-nav__item">
                        <a href="<?php echo esc_url(get_services_archive_url()); ?>" class="footer-nav__link">
                            税理士を探す
                        </a>
                    </li>
                    <li class="footer-nav__item">
                        <a href="<?php echo esc_url(home_url('#how-it-works')); ?>" class="footer-nav__link">
                            使い方
                        </a>
                    </li>
                </ul>
            </div>
            
            <?php
            // 業種タクソノミーのトップ5を表示
            $industries = get_terms([
                'taxonomy' => 'service_industry',
                'hide_empty' => true,
                'number' => 5,
                'orderby' => 'count',
                'order' => 'DESC',
            ]);
            
            if (!empty($industries) && !is_wp_error($industries)):
            ?>
            <div class="footer-nav__section">
                <h3 class="footer-nav__title">業種から探す</h3>
                <ul class="footer-nav__menu">
                    <?php foreach ($industries as $industry): ?>
                        <li class="footer-nav__item">
                            <a href="<?php echo esc_url(get_term_link($industry)); ?>" class="footer-nav__link">
                                <?php echo esc_html($industry->name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php
            // 課題タクソノミーのトップ5を表示
            $issues = get_terms([
                'taxonomy' => 'service_issue',
                'hide_empty' => true,
                'number' => 5,
                'orderby' => 'count',
                'order' => 'DESC',
            ]);
            
            if (!empty($issues) && !is_wp_error($issues)):
            ?>
            <div class="footer-nav__section">
                <h3 class="footer-nav__title">課題から探す</h3>
                <ul class="footer-nav__menu">
                    <?php foreach ($issues as $issue): ?>
                        <li class="footer-nav__item">
                            <a href="<?php echo esc_url(get_term_link($issue)); ?>" class="footer-nav__link">
                                <?php echo esc_html($issue->name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </nav>
        
        <!-- サイト情報 -->
        <div class="site-info">
            <p class="site-info__text">
                &copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. All rights reserved.
            </p>
        </div>
        
    </div>
</footer>

<?php wp_footer(); ?>

<!-- FAQ アコーディオン用JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ アコーディオン
    const faqToggles = document.querySelectorAll('.faq-item__toggle');
    
    faqToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            const answer = this.parentElement.nextElementSibling;
            
            // トグル状態を切り替え
            this.setAttribute('aria-expanded', !isExpanded);
            
            // 回答の表示/非表示を切り替え
            if (isExpanded) {
                answer.style.display = 'none';
            } else {
                answer.style.display = 'block';
            }
        });
    });
    
    // モバイルメニュートグル
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const siteNav = document.querySelector('.site-nav');
    
    if (mobileMenuToggle && siteNav) {
        mobileMenuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            siteNav.classList.toggle('is-open');
        });
    }
    
    // スムーズスクロール
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    
    smoothScrollLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

</body>
</html>
