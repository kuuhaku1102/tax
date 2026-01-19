<?php
/**
 * 見積もり依頼機能
 * 
 * 設計思想:
 * - シンプルなフォーム送信
 * - 管理画面で見積もり依頼を管理
 * - メール通知機能
 * 
 * @package TaxMatchingTheme
 */

/**
 * 見積もり依頼用カスタム投稿タイプを登録
 */
add_action('init', function() {
    register_post_type('estimate_request', array(
        'labels' => array(
            'name' => '見積もり依頼',
            'singular_name' => '見積もり依頼',
            'add_new' => '新規追加',
            'add_new_item' => '新しい見積もり依頼を追加',
            'edit_item' => '見積もり依頼を編集',
            'new_item' => '新しい見積もり依頼',
            'view_item' => '見積もり依頼を表示',
            'search_items' => '見積もり依頼を検索',
            'not_found' => '見積もり依頼が見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱に見積もり依頼はありません',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-email-alt',
        'menu_position' => 25,
        'supports' => array('title'),
        'capability_type' => 'post',
        'capabilities' => array(
            'create_posts' => 'do_not_allow',
        ),
        'map_meta_cap' => true,
    ));
});

/**
 * 見積もり依頼フォームを処理
 */
add_action('wp_ajax_submit_estimate_request', 'handle_estimate_request');
add_action('wp_ajax_nopriv_submit_estimate_request', 'handle_estimate_request');

function handle_estimate_request() {
    // ノンス検証
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'estimate_request_nonce')) {
        wp_send_json_error(array('message' => 'セキュリティチェックに失敗しました。'));
        return;
    }
    
    // データの取得とサニタイズ
    $service_type = sanitize_text_field($_POST['service_type'] ?? '');
    $company_name = sanitize_text_field($_POST['company_name'] ?? '');
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $prefecture = sanitize_text_field($_POST['prefecture'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    
    // バリデーション
    if (empty($service_type) || empty($name) || empty($email)) {
        wp_send_json_error(array('message' => '必須項目を入力してください。'));
        return;
    }
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => '有効なメールアドレスを入力してください。'));
        return;
    }
    
    // 投稿を作成
    $post_id = wp_insert_post(array(
        'post_type' => 'estimate_request',
        'post_title' => sprintf('%s - %s様', $service_type, $name),
        'post_status' => 'publish',
        'meta_input' => array(
            'service_type' => $service_type,
            'company_name' => $company_name,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'prefecture' => $prefecture,
            'message' => $message,
            'submitted_at' => current_time('mysql'),
        ),
    ));
    
    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => '送信に失敗しました。もう一度お試しください。'));
        return;
    }
    
    // 管理者にメール通知
    $admin_email = get_option('admin_email');
    $subject = sprintf('[%s] 新しい見積もり依頼: %s', get_bloginfo('name'), $service_type);
    $body = sprintf(
        "新しい見積もり依頼が届きました。\n\n" .
        "サービス種別: %s\n" .
        "会社名: %s\n" .
        "お名前: %s\n" .
        "メールアドレス: %s\n" .
        "電話番号: %s\n" .
        "都道府県: %s\n" .
        "ご要望・ご質問:\n%s\n\n" .
        "管理画面で確認: %s",
        $service_type,
        $company_name,
        $name,
        $email,
        $phone,
        $prefecture,
        $message,
        admin_url('edit.php?post_type=estimate_request')
    );
    
    wp_mail($admin_email, $subject, $body);
    
    // ユーザーに確認メール
    $user_subject = sprintf('[%s] 見積もり依頼を受け付けました', get_bloginfo('name'));
    $user_body = sprintf(
        "%s様\n\n" .
        "この度は、%sにお見積もり依頼をいただき、誠にありがとうございます。\n\n" .
        "ご依頼内容を確認の上、担当者より3営業日以内にご連絡させていただきます。\n\n" .
        "【ご依頼内容】\n" .
        "サービス種別: %s\n" .
        "会社名: %s\n" .
        "お名前: %s\n" .
        "メールアドレス: %s\n" .
        "電話番号: %s\n" .
        "都道府県: %s\n\n" .
        "何かご不明な点がございましたら、お気軽にお問い合わせください。\n\n" .
        "今後とも、よろしくお願いいたします。\n\n" .
        "---\n" .
        "%s\n" .
        "%s",
        $name,
        get_bloginfo('name'),
        $service_type,
        $company_name,
        $name,
        $email,
        $phone,
        $prefecture,
        get_bloginfo('name'),
        home_url()
    );
    
    wp_mail($email, $user_subject, $user_body);
    
    wp_send_json_success(array('message' => '見積もり依頼を受け付けました。確認メールをお送りしましたので、ご確認ください。'));
}

/**
 * 見積もり依頼の管理画面カラムをカスタマイズ
 */
add_filter('manage_estimate_request_posts_columns', function($columns) {
    return array(
        'cb' => $columns['cb'],
        'title' => 'タイトル',
        'service_type' => 'サービス種別',
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'phone' => '電話番号',
        'prefecture' => '都道府県',
        'date' => '受付日時',
    );
});

add_action('manage_estimate_request_posts_custom_column', function($column, $post_id) {
    switch ($column) {
        case 'service_type':
            echo esc_html(get_post_meta($post_id, 'service_type', true));
            break;
        case 'name':
            echo esc_html(get_post_meta($post_id, 'name', true));
            break;
        case 'email':
            $email = get_post_meta($post_id, 'email', true);
            echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            break;
        case 'phone':
            echo esc_html(get_post_meta($post_id, 'phone', true));
            break;
        case 'prefecture':
            echo esc_html(get_post_meta($post_id, 'prefecture', true));
            break;
    }
}, 10, 2);

/**
 * 見積もり依頼の詳細表示
 */
add_action('add_meta_boxes', function() {
    add_meta_box(
        'estimate_request_details',
        '見積もり依頼詳細',
        function($post) {
            $service_type = get_post_meta($post->ID, 'service_type', true);
            $company_name = get_post_meta($post->ID, 'company_name', true);
            $name = get_post_meta($post->ID, 'name', true);
            $email = get_post_meta($post->ID, 'email', true);
            $phone = get_post_meta($post->ID, 'phone', true);
            $prefecture = get_post_meta($post->ID, 'prefecture', true);
            $message = get_post_meta($post->ID, 'message', true);
            $submitted_at = get_post_meta($post->ID, 'submitted_at', true);
            
            echo '<table class="form-table">';
            echo '<tr><th>サービス種別</th><td>' . esc_html($service_type) . '</td></tr>';
            echo '<tr><th>会社名</th><td>' . esc_html($company_name) . '</td></tr>';
            echo '<tr><th>お名前</th><td>' . esc_html($name) . '</td></tr>';
            echo '<tr><th>メールアドレス</th><td><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></td></tr>';
            echo '<tr><th>電話番号</th><td>' . esc_html($phone) . '</td></tr>';
            echo '<tr><th>都道府県</th><td>' . esc_html($prefecture) . '</td></tr>';
            echo '<tr><th>受付日時</th><td>' . esc_html($submitted_at) . '</td></tr>';
            echo '<tr><th>ご要望・ご質問</th><td>' . nl2br(esc_html($message)) . '</td></tr>';
            echo '</table>';
        },
        'estimate_request',
        'normal',
        'high'
    );
});
