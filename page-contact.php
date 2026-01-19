<?php
/**
 * Template Name: お問い合わせ
 * 
 * @package TaxMatchingTheme
 */

get_header();
?>

<div class="contact-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">お問い合わせ</h1>
            <p class="page-description">
                ご質問・ご相談がございましたら、お気軽にお問い合わせください。<br>
                3営業日以内にご返信いたします。
            </p>
        </div>

        <div class="contact-form-wrapper">
            <form id="contact-form" class="contact-form">
                <?php wp_nonce_field('contact_nonce', 'contact_nonce_field'); ?>
                
                <div class="form-group required">
                    <label for="contact_name">お名前</label>
                    <input type="text" id="contact_name" name="name" required placeholder="山田 太郎">
                </div>

                <div class="form-group required">
                    <label for="contact_email">メールアドレス</label>
                    <input type="email" id="contact_email" name="email" required placeholder="example@example.com">
                </div>

                <div class="form-group">
                    <label for="contact_phone">電話番号</label>
                    <input type="tel" id="contact_phone" name="phone" placeholder="03-1234-5678">
                </div>

                <div class="form-group required">
                    <label for="contact_subject">件名</label>
                    <input type="text" id="contact_subject" name="subject" required placeholder="お問い合わせの件名">
                </div>

                <div class="form-group required">
                    <label for="contact_message">お問い合わせ内容</label>
                    <textarea id="contact_message" name="message" rows="8" required placeholder="お問い合わせ内容をご記入ください"></textarea>
                </div>

                <div class="privacy-notice">
                    <p>
                        <a href="<?php echo home_url('/privacy-policy/'); ?>" target="_blank">プライバシーポリシー</a>に同意の上、送信してください。
                    </p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">
                        <span class="btn-text">送信する</span>
                        <span class="btn-loading" style="display:none;">送信中...</span>
                    </button>
                </div>

                <div id="contact-message" class="form-message" style="display:none;"></div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    const messageDiv = document.getElementById('contact-message');
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
        formData.append('action', 'submit_contact');
        formData.append('nonce', document.getElementById('contact_nonce_field').value);

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
