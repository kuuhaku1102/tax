<?php
/**
 * Template Name: 利用の流れ
 * 
 * @package TaxMatchingTheme
 */

get_header();
?>

<div class="how-to-use-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">ご利用までの流れ</h1>
            <p class="page-description">
                税理士マッチは、たった3ステップで最適な税理士が見つかります。<br>
                完全無料でご利用いただけます。
            </p>
        </div>

        <div class="flow-steps-detailed">
            <div class="flow-step-detailed">
                <div class="step-visual">
                    <div class="step-number-large">1</div>
                    <div class="step-icon">📝</div>
                </div>
                <div class="step-content">
                    <h2 class="step-title">ご依頼内容を送信</h2>
                    <p class="step-description">
                        ご依頼したい内容について、依頼フォームに沿って回答します。<br>
                        所要時間は約3分です。
                    </p>
                    <ul class="step-details">
                        <li>サービス種別を選択</li>
                        <li>ご要望・ご質問を入力</li>
                        <li>お客様情報を入力</li>
                    </ul>
                </div>
            </div>

            <div class="flow-arrow-large">↓</div>

            <div class="flow-step-detailed">
                <div class="step-visual">
                    <div class="step-number-large">2</div>
                    <div class="step-icon">📧</div>
                </div>
                <div class="step-content">
                    <h2 class="step-title">見積もりを受領</h2>
                    <p class="step-description">
                        依頼内容に応じた見積もりがメールに届きます。<br>
                        通常、3営業日以内にご連絡いたします。
                    </p>
                    <ul class="step-details">
                        <li>複数の税理士から見積もりが届く</li>
                        <li>料金、経験、対応エリアを比較</li>
                        <li>税理士のプロフィールを確認</li>
                    </ul>
                </div>
            </div>

            <div class="flow-arrow-large">↓</div>

            <div class="flow-step-detailed">
                <div class="step-visual">
                    <div class="step-number-large">3</div>
                    <div class="step-icon">🤝</div>
                </div>
                <div class="step-content">
                    <h2 class="step-title">税理士へのご依頼</h2>
                    <p class="step-description">
                        見積もりをご検討いただいた後で、税理士にご発注いただけます。<br>
                        ※必ずご発注いただく必要はございません
                    </p>
                    <ul class="step-details">
                        <li>気に入った税理士を選択</li>
                        <li>契約内容を確認</li>
                        <li>サービス開始</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="features-section">
            <h2 class="section-title">税理士マッチの特徴</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3 class="feature-title">完全無料</h3>
                    <p class="feature-description">
                        初期費用、月額費用、成功報酬費用は一切かかりません。
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">スピーディー</h3>
                    <p class="feature-description">
                        最短即日で税理士から見積もりが届きます。
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">最適なマッチング</h3>
                    <p class="feature-description">
                        あなたの業種・課題に精通した税理士をご紹介します。
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">安心・安全</h3>
                    <p class="feature-description">
                        厳選された税理士のみが登録しています。
                    </p>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <h2 class="cta-title">まずは無料でお見積もり</h2>
            <p class="cta-description">
                完全無料で、あなたに最適な税理士をご紹介します。
            </p>
            <a href="<?php echo home_url('/estimate/'); ?>" class="btn btn-primary btn-large">
                無料で見積もりを依頼する
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
