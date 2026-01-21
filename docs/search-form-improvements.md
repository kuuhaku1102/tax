# 検索フォームの改善

## 概要

検索フォームに得意業種と得意分野のフィルターを追加し、対応エリアフィルターを削除しました。

## 変更内容

### 削除したフィルター
- ❌ **対応エリア**（都道府県と重複するため削除）

### 追加したフィルター
- ✅ **得意業種**（税理士事務所の得意業種から選択）
- ✅ **得意分野**（税理士事務所の得意分野から選択）

### 修正後の検索フォーム構成

| フィルター | 対象 | 説明 |
|-----------|------|------|
| 業種 | 税理士サービス + 税理士事務所 | 税理士サービスの業種タクソノミー |
| 課題・目的 | 税理士サービス | 税理士サービスの課題タクソノミー |
| **得意業種** | 税理士事務所 | 税理士事務所の得意業種から選択 |
| **得意分野** | 税理士事務所 | 税理士事務所の得意分野から選択 |
| 都道府県 | 税理士事務所 | 税理士事務所の所在都道府県 |
| 並び順 | 両方 | おすすめ順、新着順、名前順 |

## 実装詳細

### 1. ヘルパー関数の追加

`/inc/helpers.php`に以下の関数を追加：

#### `get_all_office_services()`
全税理士事務所の得意分野を取得します。

```php
function get_all_office_services() {
    global $wpdb;
    
    $services = array();
    
    // すべての税理士事務所の得意分野を取得
    $results = $wpdb->get_results("
        SELECT meta_value 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_tax_office_services' 
        AND meta_value != ''
    ");
    
    foreach ($results as $result) {
        $services_array = json_decode($result->meta_value, true);
        if (is_array($services_array)) {
            $services = array_merge($services, $services_array);
        }
    }
    
    // 重複を削除してソート
    $services = array_unique($services);
    sort($services);
    
    return $services;
}
```

#### `get_all_office_industries()`
全税理士事務所の得意業種を取得します。

```php
function get_all_office_industries() {
    global $wpdb;
    
    $industries = array();
    
    // すべての税理士事務所の得意業種を取得
    $results = $wpdb->get_results("
        SELECT meta_value 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_tax_office_industries' 
        AND meta_value != ''
    ");
    
    foreach ($results as $result) {
        $industries_array = json_decode($result->meta_value, true);
        if (is_array($industries_array)) {
            $industries = array_merge($industries, $industries_array);
        }
    }
    
    // 重複を削除してソート
    $industries = array_unique($industries);
    sort($industries);
    
    return $industries;
}
```

### 2. 検索フォームの修正

`/archive-tax_service.php`の検索フォームに以下を追加：

```html
<!-- 得意業種フィルター -->
<div class="service-filter__field">
    <label class="service-filter__label">得意業種</label>
    <select name="office_industry" class="service-filter__select">
        <option value="">すべて</option>
        <?php
        if (function_exists('get_all_office_industries')):
            $industries = get_all_office_industries();
            foreach ($industries as $industry):
                $selected = isset($_GET['office_industry']) && $_GET['office_industry'] == $industry ? 'selected' : '';
        ?>
                <option value="<?php echo esc_attr($industry); ?>" <?php echo $selected; ?>>
                    <?php echo esc_html($industry); ?>
                </option>
        <?php
            endforeach;
        endif;
        ?>
    </select>
</div>

<!-- 得意分野フィルター -->
<div class="service-filter__field">
    <label class="service-filter__label">得意分野</label>
    <select name="office_service" class="service-filter__select">
        <option value="">すべて</option>
        <?php
        if (function_exists('get_all_office_services')):
            $services = get_all_office_services();
            foreach ($services as $service):
                $selected = isset($_GET['office_service']) && $_GET['office_service'] == $service ? 'selected' : '';
        ?>
                <option value="<?php echo esc_attr($service); ?>" <?php echo $selected; ?>>
                    <?php echo esc_html($service); ?>
                </option>
        <?php
            endforeach;
        endif;
        ?>
    </select>
</div>
```

### 3. 検索ロジックの修正

税理士事務所の検索クエリに以下のロジックを追加：

```php
// 得意業種・得意分野でのメタクエリ検索
$meta_query = array('relation' => 'AND');

// 得意業種で絞り込み
if (isset($_GET['office_industry']) && !empty($_GET['office_industry'])) {
    $meta_query[] = array(
        'key' => '_tax_office_industries',
        'value' => $_GET['office_industry'],
        'compare' => 'LIKE',
    );
}

// 得意分野で絞り込み
if (isset($_GET['office_service']) && !empty($_GET['office_service'])) {
    $meta_query[] = array(
        'key' => '_tax_office_services',
        'value' => $_GET['office_service'],
        'compare' => 'LIKE',
    );
}

// 業種フィルター（税理士サービス用）でも検索
if (isset($_GET['service_industry']) && !empty($_GET['service_industry'])) {
    $search_term = get_term_by('slug', $_GET['service_industry'], 'service_industry');
    if ($search_term) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key' => '_tax_office_services',
                'value' => $search_term->name,
                'compare' => 'LIKE',
            ),
            array(
                'key' => '_tax_office_industries',
                'value' => $search_term->name,
                'compare' => 'LIKE',
            ),
        );
    }
}

if (count($meta_query) > 1) {
    $office_query_args['meta_query'] = $meta_query;
}
```

## 検索パラメータ

| パラメータ | 説明 | 例 |
|-----------|------|-----|
| `service_industry` | 業種（タクソノミースラッグ） | `sns-marketing` |
| `service_issue` | 課題・目的（タクソノミースラッグ） | `startup` |
| `office_industry` | 得意業種（文字列） | `建設業` |
| `office_service` | 得意分野（文字列） | `法人決算` |
| `office_prefecture` | 都道府県（タクソノミースラッグ） | `tokyo` |
| `orderby` | 並び順 | `priority`, `date`, `title` |

## 使い方

### 基本的な検索
1. `/services/`ページにアクセス
2. 検索フォームで条件を選択
3. 「検索する」ボタンをクリック

### 得意業種での検索
1. 「得意業種」フィルターで業種を選択（例: 「建設業」）
2. 「検索する」ボタンをクリック
3. 得意業種に「建設業」を含む税理士事務所が表示される

### 得意分野での検索
1. 「得意分野」フィルターで分野を選択（例: 「法人決算」）
2. 「検索する」ボタンをクリック
3. 得意分野に「法人決算」を含む税理士事務所が表示される

### 複数条件での検索
1. 「得意業種」で「建設業」を選択
2. 「得意分野」で「法人決算」を選択
3. 「都道府県」で「東京都」を選択
4. 「検索する」ボタンをクリック
5. すべての条件に一致する税理士事務所が表示される

## メリット

1. **より詳細な検索**: 得意業種と得意分野で絞り込める
2. **重複排除**: 対応エリアと都道府県の重複を解消
3. **実データに基づく選択肢**: 実際に登録されている得意業種・得意分野から選択
4. **柔軟な検索**: 複数条件を組み合わせて検索可能

## 注意事項

- 得意業種・得意分野の選択肢は、実際にインポートされた税理士事務所データから動的に生成されます
- データがインポートされていない場合、選択肢は表示されません
- 検索は`LIKE`比較を使用しているため、部分一致で検索されます

## パフォーマンス

- ヘルパー関数はデータベースクエリを実行するため、キャッシュの実装を推奨します
- 将来的にはTransient APIを使用してキャッシュを実装する予定です

---

**更新日**: 2026年1月20日  
**コミットハッシュ**: a4e7c27
