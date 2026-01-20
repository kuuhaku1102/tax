<?php
/**
 * 税理士・会計事務所カスタム投稿タイプ
 */

// カスタム投稿タイプの登録
function register_tax_office_post_type() {
    $labels = array(
        'name'                  => '税理士・会計事務所',
        'singular_name'         => '税理士・会計事務所',
        'menu_name'             => '税理士事務所',
        'name_admin_bar'        => '税理士事務所',
        'add_new'               => '新規追加',
        'add_new_item'          => '新規税理士事務所を追加',
        'new_item'              => '新規税理士事務所',
        'edit_item'             => '税理士事務所を編集',
        'view_item'             => '税理士事務所を表示',
        'all_items'             => 'すべての税理士事務所',
        'search_items'          => '税理士事務所を検索',
        'parent_item_colon'     => '親税理士事務所:',
        'not_found'             => '税理士事務所が見つかりませんでした。',
        'not_found_in_trash'    => 'ゴミ箱に税理士事務所が見つかりませんでした。'
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'tax-office'),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-building',
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'          => true,
    );

    register_post_type('tax_office', $args);
}
add_action('init', 'register_tax_office_post_type');

// カスタムタクソノミーの登録
function register_tax_office_taxonomies() {
    // 所在都道府県
    register_taxonomy('office_prefecture', 'tax_office', array(
        'labels' => array(
            'name'              => '所在都道府県',
            'singular_name'     => '都道府県',
            'search_items'      => '都道府県を検索',
            'all_items'         => 'すべての都道府県',
            'edit_item'         => '都道府県を編集',
            'update_item'       => '都道府県を更新',
            'add_new_item'      => '新規都道府県を追加',
            'new_item_name'     => '新規都道府県名',
            'menu_name'         => '所在都道府県',
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => false,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'office-prefecture'),
        'show_in_rest'      => true,
    ));

    // 対応可能サービス（依頼内容）
    register_taxonomy('office_service', 'tax_office', array(
        'labels' => array(
            'name'              => '対応可能サービス',
            'singular_name'     => 'サービス',
            'search_items'      => 'サービスを検索',
            'all_items'         => 'すべてのサービス',
            'edit_item'         => 'サービスを編集',
            'update_item'       => 'サービスを更新',
            'add_new_item'      => '新規サービスを追加',
            'new_item_name'     => '新規サービス名',
            'menu_name'         => '対応サービス',
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => false,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'office-service'),
        'show_in_rest'      => true,
    ));

    // 対応業種
    register_taxonomy('office_industry', 'tax_office', array(
        'labels' => array(
            'name'              => '対応業種',
            'singular_name'     => '業種',
            'search_items'      => '業種を検索',
            'all_items'         => 'すべての業種',
            'edit_item'         => '業種を編集',
            'update_item'       => '業種を更新',
            'add_new_item'      => '新規業種を追加',
            'new_item_name'     => '新規業種名',
            'menu_name'         => '対応業種',
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => false,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'office-industry'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'register_tax_office_taxonomies');

// カスタムフィールドの追加（メタボックス）
function add_tax_office_meta_boxes() {
    add_meta_box(
        'tax_office_info',
        '事務所情報',
        'render_tax_office_info_meta_box',
        'tax_office',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_tax_office_meta_boxes');

// 事務所情報メタボックスの表示
function render_tax_office_info_meta_box($post) {
    wp_nonce_field('tax_office_info_nonce', 'tax_office_info_nonce_field');
    
    $address = get_post_meta($post->ID, '_tax_office_address', true);
    $phone = get_post_meta($post->ID, '_tax_office_phone', true);
    $email = get_post_meta($post->ID, '_tax_office_email', true);
    $website = get_post_meta($post->ID, '_tax_office_website', true);
    $source_url = get_post_meta($post->ID, '_tax_office_source_url', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="tax_office_address">所在地</label></th>
            <td>
                <input type="text" id="tax_office_address" name="tax_office_address" 
                       value="<?php echo esc_attr($address); ?>" class="large-text">
            </td>
        </tr>
        <tr>
            <th><label for="tax_office_phone">電話番号</label></th>
            <td>
                <input type="text" id="tax_office_phone" name="tax_office_phone" 
                       value="<?php echo esc_attr($phone); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="tax_office_email">メールアドレス</label></th>
            <td>
                <input type="email" id="tax_office_email" name="tax_office_email" 
                       value="<?php echo esc_attr($email); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="tax_office_website">ウェブサイト</label></th>
            <td>
                <input type="url" id="tax_office_website" name="tax_office_website" 
                       value="<?php echo esc_attr($website); ?>" class="large-text">
            </td>
        </tr>
        <tr>
            <th><label for="tax_office_source_url">元データURL</label></th>
            <td>
                <input type="url" id="tax_office_source_url" name="tax_office_source_url" 
                       value="<?php echo esc_attr($source_url); ?>" class="large-text">
                <p class="description">スクレイピング元のURL（参照用）</p>
            </td>
        </tr>
    </table>
    <?php
}

// カスタムフィールドの保存
function save_tax_office_meta_boxes($post_id) {
    // ノンスの検証
    if (!isset($_POST['tax_office_info_nonce_field']) || 
        !wp_verify_nonce($_POST['tax_office_info_nonce_field'], 'tax_office_info_nonce')) {
        return;
    }

    // 自動保存の場合は処理しない
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // データの保存
    $fields = array('address', 'phone', 'email', 'website', 'source_url');
    
    foreach ($fields as $field) {
        $key = 'tax_office_' . $field;
        if (isset($_POST[$key])) {
            update_post_meta($post_id, '_' . $key, sanitize_text_field($_POST[$key]));
        }
    }
}
add_action('save_post_tax_office', 'save_tax_office_meta_boxes');

// 管理画面の一覧カラムをカスタマイズ
function customize_tax_office_columns($columns) {
    // デフォルトのタクソノミーカラムを削除
    unset($columns['taxonomy-office_prefecture']);
    unset($columns['taxonomy-office_service']);
    unset($columns['taxonomy-office_industry']);
    
    // 新しいカラムを追加
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // タイトルの後にカスタムカラムを追加
        if ($key === 'title') {
            $new_columns['prefecture'] = '所在都道府県';
            $new_columns['services'] = '対応可能サービス';
            $new_columns['industries'] = '対応業種';
        }
    }
    
    return $new_columns;
}
add_filter('manage_tax_office_posts_columns', 'customize_tax_office_columns');

// カスタムカラムの内容を表示
function display_tax_office_custom_columns($column, $post_id) {
    switch ($column) {
        case 'prefecture':
            $terms = get_the_terms($post_id, 'office_prefecture');
            if ($terms && !is_wp_error($terms)) {
                $term_names = array();
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                echo esc_html(implode(', ', $term_names));
            } else {
                echo '—';
            }
            break;
            
        case 'services':
            $terms = get_the_terms($post_id, 'office_service');
            if ($terms && !is_wp_error($terms)) {
                $term_names = array();
                $count = 0;
                foreach ($terms as $term) {
                    if ($count < 3) {
                        $term_names[] = $term->name;
                        $count++;
                    }
                }
                echo esc_html(implode(', ', $term_names));
                if (count($terms) > 3) {
                    echo ' <span style="color: #999;">他' . (count($terms) - 3) . '件</span>';
                }
            } else {
                echo '—';
            }
            break;
            
        case 'industries':
            $terms = get_the_terms($post_id, 'office_industry');
            if ($terms && !is_wp_error($terms)) {
                $term_names = array();
                $count = 0;
                foreach ($terms as $term) {
                    if ($count < 3) {
                        $term_names[] = $term->name;
                        $count++;
                    }
                }
                echo esc_html(implode(', ', $term_names));
                if (count($terms) > 3) {
                    echo ' <span style="color: #999;">他' . (count($terms) - 3) . '件</span>';
                }
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_tax_office_posts_custom_column', 'display_tax_office_custom_columns', 10, 2);

// カラムのソート機能を追加
function make_tax_office_columns_sortable($columns) {
    $columns['prefecture'] = 'office_prefecture';
    return $columns;
}
add_filter('manage_edit-tax_office_sortable_columns', 'make_tax_office_columns_sortable');
