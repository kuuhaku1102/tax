<?php
/**
 * Template Name: 見積もり依頼フォーム
 * 
 * 設計思想:
 * - シンプルで使いやすいフォーム
 * - Ajax送信でUX向上
 * - バリデーションとエラーハンドリング
 * 
 * @package TaxMatchingTheme
 */

get_header();
?>

<div class="estimate-request-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">無料お見積もり依頼</h1>
            <p class="page-description">
                ご依頼内容を入力いただくと、最適な税理士から見積もりが届きます。<br>
                完全無料でご利用いただけます。
            </p>
        </div>

        <div class="estimate-form-wrapper">
            <form id="estimate-request-form" class="estimate-form">
                <?php wp_nonce_field('estimate_request_nonce', 'estimate_nonce'); ?>
                
                <div class="form-section">
                    <h2 class="section-title">ご依頼内容</h2>
                    
                    <div class="form-group required">
                        <label for="service_type">サービス種別</label>
                        <select id="service_type" name="service_type" required>
                            <option value="">選択してください</option>
                            <option value="顧問税理士">顧問税理士</option>
                            <option value="確定申告">確定申告</option>
                            <option value="決算申告">決算申告</option>
                            <option value="相続税申告">相続税申告</option>
                            <option value="資金調達">資金調達</option>
                            <option value="補助金・助成金">補助金・助成金</option>
                            <option value="その他">その他</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">ご要望・ご質問</label>
                        <textarea id="message" name="message" rows="5" placeholder="具体的なご要望やご質問がありましたら、ご記入ください。"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2 class="section-title">お客様情報</h2>
                    
                    <div class="form-group">
                        <label for="company_name">会社名・屋号</label>
                        <input type="text" id="company_name" name="company_name" placeholder="株式会社○○">
                    </div>

                    <div class="form-group required">
                        <label for="name">お名前</label>
                        <input type="text" id="name" name="name" required placeholder="山田 太郎">
                    </div>

                    <div class="form-group required">
                        <label for="email">メールアドレス</label>
                        <input type="email" id="email" name="email" required placeholder="example@example.com">
                    </div>

                    <div class="form-group">
                        <label for="phone">電話番号</label>
                        <input type="tel" id="phone" name="phone" placeholder="03-1234-5678">
                    </div>

                    <div class="form-group">
                        <label for="prefecture">都道府県</label>
                        <select id="prefecture" name="prefecture">
                            <option value="">選択してください</option>
                            <option value="北海道">北海道</option>
                            <option value="青森県">青森県</option>
                            <option value="岩手県">岩手県</option>
                            <option value="宮城県">宮城県</option>
                            <option value="秋田県">秋田県</option>
                            <option value="山形県">山形県</option>
                            <option value="福島県">福島県</option>
                            <option value="茨城県">茨城県</option>
                            <option value="栃木県">栃木県</option>
                            <option value="群馬県">群馬県</option>
                            <option value="埼玉県">埼玉県</option>
                            <option value="千葉県">千葉県</option>
                            <option value="東京都">東京都</option>
                            <option value="神奈川県">神奈川県</option>
                            <option value="新潟県">新潟県</option>
                            <option value="富山県">富山県</option>
                            <option value="石川県">石川県</option>
                            <option value="福井県">福井県</option>
                            <option value="山梨県">山梨県</option>
                            <option value="長野県">長野県</option>
                            <option value="岐阜県">岐阜県</option>
                            <option value="静岡県">静岡県</option>
                            <option value="愛知県">愛知県</option>
                            <option value="三重県">三重県</option>
                            <option value="滋賀県">滋賀県</option>
                            <option value="京都府">京都府</option>
                            <option value="大阪府">大阪府</option>
                            <option value="兵庫県">兵庫県</option>
                            <option value="奈良県">奈良県</option>
                            <option value="和歌山県">和歌山県</option>
                            <option value="鳥取県">鳥取県</option>
                            <option value="島根県">島根県</option>
                            <option value="岡山県">岡山県</option>
                            <option value="広島県">広島県</option>
                            <option value="山口県">山口県</option>
                            <option value="徳島県">徳島県</option>
                            <option value="香川県">香川県</option>
                            <option value="愛媛県">愛媛県</option>
                            <option value="高知県">高知県</option>
                            <option value="福岡県">福岡県</option>
                            <option value="佐賀県">佐賀県</option>
                            <option value="長崎県">長崎県</option>
                            <option value="熊本県">熊本県</option>
                            <option value="大分県">大分県</option>
                            <option value="宮崎県">宮崎県</option>
                            <option value="鹿児島県">鹿児島県</option>
                            <option value="沖縄県">沖縄県</option>
                            <option value="全国対応">全国対応</option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <div class="privacy-notice">
                        <p>
                            <a href="<?php echo home_url('/privacy-policy/'); ?>" target="_blank">プライバシーポリシー</a>に同意の上、送信してください。
                        </p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <span class="btn-text">無料で見積もりを依頼する</span>
                            <span class="btn-loading" style="display:none;">送信中...</span>
                        </button>
                    </div>
                </div>

                <div id="form-message" class="form-message" style="display:none;"></div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('estimate-request-form');
    const messageDiv = document.getElementById('form-message');
    const submitBtn = form.querySelector('button[type="submit"]');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // ボタンを無効化
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        messageDiv.style.display = 'none';

        // フォームデータを取得
        const formData = new FormData(form);
        formData.append('action', 'submit_estimate_request');
        formData.append('nonce', document.getElementById('estimate_nonce').value);

        // Ajax送信
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.style.display = 'block';
            
            if (data.success) {
                messageDiv.className = 'form-message success';
                messageDiv.textContent = data.data.message;
                form.reset();
            } else {
                messageDiv.className = 'form-message error';
                messageDiv.textContent = data.data.message;
            }
        })
        .catch(error => {
            messageDiv.style.display = 'block';
            messageDiv.className = 'form-message error';
            messageDiv.textContent = 'エラーが発生しました。もう一度お試しください。';
        })
        .finally(() => {
            // ボタンを有効化
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        });
    });
});
</script>

<?php get_footer(); ?>
