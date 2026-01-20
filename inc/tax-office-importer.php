<?php
/**
 * 税理士・会計事務所データインポート機能
 */

// 管理画面にインポートページを追加
function add_tax_office_importer_menu() {
    add_submenu_page(
        'edit.php?post_type=tax_office',
        'データインポート',
        'データインポート',
        'manage_options',
        'tax-office-importer',
        'render_tax_office_importer_page'
    );
}
add_action('admin_menu', 'add_tax_office_importer_menu');

// インポートページの表示
function render_tax_office_importer_page() {
    ?>
    <div class="wrap">
        <h1>税理士・会計事務所データインポート</h1>
        
        <?php
        // インポート処理の実行
        if (isset($_POST['import_tax_offices']) && check_admin_referer('import_tax_offices_action', 'import_tax_offices_nonce')) {
            $result = import_tax_offices_from_uploaded_files();
            
            if ($result['success']) {
                echo '<div class="notice notice-success"><p>';
                echo sprintf('インポート完了: %d件の事務所を登録しました。', $result['imported']);
                if ($result['skipped'] > 0) {
                    echo sprintf(' (%d件はスキップされました)', $result['skipped']);
                }
                if (!empty($result['errors'])) {
                    echo '<br>エラー: ' . implode(', ', array_slice($result['errors'], 0, 5));
                    if (count($result['errors']) > 5) {
                        echo sprintf(' (他%d件)', count($result['errors']) - 5);
                    }
                }
                echo '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>エラー: ' . esc_html($result['message']) . '</p></div>';
            }
        }
        ?>
        
        <div class="card" style="max-width: 800px;">
            <h2>JSONファイルからインポート</h2>
            <p>弥生の税理士紹介サイトからスクレイピングしたJSONファイルをアップロードしてインポートします。</p>
            
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('import_tax_offices_action', 'import_tax_offices_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">JSONファイル</th>
                        <td>
                            <input type="file" name="json_files[]" accept=".json" multiple required>
                            <p class="description">複数のJSONファイルを選択できます。Ctrl/Cmdキーを押しながらクリックして複数選択してください。</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">インポート設定</th>
                        <td>
                            <label>
                                <input type="checkbox" name="skip_existing" value="1" checked>
                                既存の事務所をスキップする
                            </label>
                            <p class="description">同じ名前の事務所が既に存在する場合、インポートをスキップします。</p>
                            <br>
                            <label>
                                <input type="checkbox" name="publish_immediately" value="1">
                                すぐに公開する
                            </label>
                            <p class="description">チェックしない場合は下書きとして保存されます。</p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="import_tax_offices" class="button button-primary" value="インポート開始">
                </p>
            </form>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>インポートされるデータ</h2>
            <ul>
                <li>事務所名（投稿タイトル）</li>
                <li>所在都道府県（タクソノミー）</li>
                <li>対応可能サービス（タクソノミー）</li>
                <li>対応業種（タクソノミー）</li>
                <li>元データURL（カスタムフィールド）</li>
            </ul>
            <p><strong>注意:</strong> 弥生製品関連の情報は除外されます。</p>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2>現在の登録状況</h2>
            <?php
            $office_count = wp_count_posts('tax_office');
            $published = $office_count->publish ?? 0;
            $draft = $office_count->draft ?? 0;
            ?>
            <p>
                公開中: <strong><?php echo $published; ?></strong>件<br>
                下書き: <strong><?php echo $draft; ?></strong>件<br>
                合計: <strong><?php echo ($published + $draft); ?></strong>件
            </p>
        </div>
    </div>
    <?php
}

// アップロードされたJSONファイルからインポート
function import_tax_offices_from_uploaded_files() {
    if (empty($_FILES['json_files']['name'][0])) {
        return array(
            'success' => false,
            'message' => 'ファイルが選択されていません'
        );
    }
    
    $imported = 0;
    $skipped = 0;
    $errors = array();
    $skip_existing = isset($_POST['skip_existing']) && $_POST['skip_existing'] == '1';
    $publish_immediately = isset($_POST['publish_immediately']) && $_POST['publish_immediately'] == '1';
    $post_status = $publish_immediately ? 'publish' : 'draft';
    
    $files = $_FILES['json_files'];
    $file_count = count($files['name']);
    
    for ($i = 0; $i < $file_count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = $files['name'][$i] . ': アップロードエラー';
            continue;
        }
        
        $json_data = file_get_contents($files['tmp_name'][$i]);
        $data = json_decode($json_data, true);
        
        if (!$data) {
            $errors[] = $files['name'][$i] . ': JSON解析エラー';
            continue;
        }
        
        // データ構造を確認
        if (!isset($data['all_scraped_data']) || !is_array($data['all_scraped_data'])) {
            $errors[] = $files['name'][$i] . ': データ構造が不正です';
            continue;
        }
        
        $prefecture_code = $data['prefecture_code'] ?? '';
        $prefecture_name = $data['prefecture_name'] ?? '';
        
        // 都道府県タームを取得または作成
        $prefecture_term = null;
        if (!empty($prefecture_name)) {
            $prefecture_term = get_or_create_term($prefecture_name, 'office_prefecture');
        }
        
        foreach ($data['all_scraped_data'] as $office_data) {
            $office_name = $office_data['name'] ?? '';
            
            if (empty($office_name)) {
                continue;
            }
            
            // 既存チェック
            if ($skip_existing) {
                $existing = get_page_by_title($office_name, OBJECT, 'tax_office');
                if ($existing) {
                    $skipped++;
                    continue;
                }
            }
            
            // 投稿を作成
            $post_id = wp_insert_post(array(
                'post_title'    => $office_name,
                'post_type'     => 'tax_office',
                'post_status'   => $post_status,
                'post_content'  => '', // 必要に応じて説明文を追加
            ));
            
            if (is_wp_error($post_id)) {
                $errors[] = $office_name . ': 投稿作成エラー';
                continue;
            }
            
            // 都道府県を設定
            if ($prefecture_term) {
                wp_set_object_terms($post_id, $prefecture_term['term_id'], 'office_prefecture');
            }
            
            // 対応可能サービスを設定
            if (isset($office_data['details']['依頼内容']) && is_array($office_data['details']['依頼内容'])) {
                $service_terms = array();
                foreach ($office_data['details']['依頼内容'] as $service) {
                    // 弥生製品関連は除外
                    if (strpos($service, '弥生') !== false) {
                        continue;
                    }
                    $term = get_or_create_term($service, 'office_service');
                    if ($term) {
                        $service_terms[] = $term['term_id'];
                    }
                }
                if (!empty($service_terms)) {
                    wp_set_object_terms($post_id, $service_terms, 'office_service');
                }
            }
            
            // 対応業種を設定
            if (isset($office_data['details']['業種']) && is_array($office_data['details']['業種'])) {
                $industry_terms = array();
                foreach ($office_data['details']['業種'] as $industry) {
                    $term = get_or_create_term($industry, 'office_industry');
                    if ($term) {
                        $industry_terms[] = $term['term_id'];
                    }
                }
                if (!empty($industry_terms)) {
                    wp_set_object_terms($post_id, $industry_terms, 'office_industry');
                }
            }
            
            // 元データURLを保存
            if (!empty($prefecture_code)) {
                $source_url = 'https://zeirishi.yayoi-kk.co.jp/offices?prefecture_cd=' . $prefecture_code;
                update_post_meta($post_id, '_tax_office_source_url', $source_url);
            }
            
            $imported++;
        }
    }
    
    return array(
        'success' => true,
        'imported' => $imported,
        'skipped' => $skipped,
        'errors' => $errors
    );
}

// タームを取得または作成するヘルパー関数
function get_or_create_term($term_name, $taxonomy) {
    if (empty($term_name)) {
        return null;
    }
    
    // 既存のタームを検索
    $term = term_exists($term_name, $taxonomy);
    
    if (!$term) {
        // 新規作成
        $term = wp_insert_term($term_name, $taxonomy);
        
        if (is_wp_error($term)) {
            return null;
        }
    }
    
    return is_array($term) ? $term : null;
}
