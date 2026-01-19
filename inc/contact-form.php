<?php
/**
 * お問い合わせフォーム処理
 * 
 * @package TaxMatchingTheme
 */

// ========================================
// お問い合わせフォーム送信処理
// ========================================

/**
 * お問い合わせフォームのAjax処理
 */
add_action('wp_ajax_submit_contact', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact', 'handle_contact_form_submission');

function handle_contact_form_submission() {
    // Nonceチェック
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'contact_nonce')) {
        wp_send_json_error([
            'message' => 'セキュリティチェックに失敗しました。'
        ]);
        return;
    }

    // フォームデータの取得とバリデーション
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    // 必須項目のチェック
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        wp_send_json_error([
            'message' => '必須項目を入力してください。'
        ]);
        return;
    }

    // メールアドレスの形式チェック
    if (!is_email($email)) {
        wp_send_json_error([
            'message' => 'メールアドレスの形式が正しくありません。'
        ]);
        return;
    }

    // データベースに保存
    $contact_data = [
        'post_title' => $subject,
        'post_content' => $message,
        'post_type' => 'contact_inquiry',
        'post_status' => 'publish',
        'meta_input' => [
            'contact_name' => $name,
            'contact_email' => $email,
            'contact_phone' => $phone,
            'contact_subject' => $subject,
        ]
    ];

    $post_id = wp_insert_post($contact_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error([
            'message' => 'お問い合わせの送信に失敗しました。もう一度お試しください。'
        ]);
        return;
    }

    // 管理者へのメール送信
    $admin_email = get_option('admin_email');
    $email_subject = '[お問い合わせ] ' . $subject;
    $email_message = "お問い合わせがありました。\n\n";
    $email_message .= "お名前: {$name}\n";
    $email_message .= "メールアドレス: {$email}\n";
    $email_message .= "電話番号: {$phone}\n";
    $email_message .= "件名: {$subject}\n\n";
    $email_message .= "お問い合わせ内容:\n{$message}\n";

    wp_mail($admin_email, $email_subject, $email_message);

    // お客様への自動返信メール
    $customer_subject = 'お問い合わせを受け付けました';
    $customer_message = "{$name} 様\n\n";
    $customer_message .= "お問い合わせいただきありがとうございます。\n";
    $customer_message .= "以下の内容でお問い合わせを受け付けました。\n\n";
    $customer_message .= "件名: {$subject}\n\n";
    $customer_message .= "お問い合わせ内容:\n{$message}\n\n";
    $customer_message .= "3営業日以内にご返信いたしますので、しばらくお待ちください。\n\n";
    $customer_message .= "━━━━━━━━━━━━━━━━━━━━\n";
    $customer_message .= "税理士マッチ\n";
    $customer_message .= get_site_url() . "\n";
    $customer_message .= "━━━━━━━━━━━━━━━━━━━━\n";

    wp_mail($email, $customer_subject, $customer_message);

    // 成功レスポンス
    wp_send_json_success([
        'message' => 'お問い合わせを受け付けました。ご返信までしばらくお待ちください。'
    ]);
}

// ========================================
// お問い合わせカスタム投稿タイプ
// ========================================

/**
 * お問い合わせ用のカスタム投稿タイプを登録
 */
add_action('init', function () {
    register_post_type('contact_inquiry', [
        'label' => 'お問い合わせ',
        'labels' => [
            'name' => 'お問い合わせ',
            'singular_name' => 'お問い合わせ',
            'add_new' => '新規追加',
            'add_new_item' => '新しいお問い合わせを追加',
            'edit_item' => 'お問い合わせを編集',
            'new_item' => '新しいお問い合わせ',
            'view_item' => 'お問い合わせを表示',
            'search_items' => 'お問い合わせを検索',
            'not_found' => 'お問い合わせが見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱にお問い合わせはありません',
        ],
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'supports' => ['title', 'editor'],
        'menu_icon' => 'dashicons-email',
        'show_in_rest' => false,
        'menu_position' => 25,
        'capabilities' => [
            'create_posts' => false, // 管理画面から新規作成できないようにする
        ],
        'map_meta_cap' => true,
    ]);
});
