# ページネーション問題の分析レポート

## 問題

`/services/`ページでページネーションをクリックすると404エラーが発生します。

## 原因

`/services/`は`tax_service`カスタム投稿タイプのアーカイブページですが、`archive-tax_service.php`内で`tax_office`のカスタムクエリを実行しています。

WordPressは`/services/?paged=2`というURLを認識しますが、実際には`tax_service`のアーカイブページとして処理されるため、`tax_office`のページネーションが機能しません。

## 解決策

### オプション1: 固定ページテンプレートに変更（推奨）

1. `archive-tax_service.php`を`page-services.php`に変更
2. WordPressで「サービス」という固定ページを作成
3. スラッグを`services`に設定
4. テンプレートを「サービスページ」に設定

### オプション2: 税理士サービスのアーカイブスラッグを変更

1. `tax_service`のアーカイブスラッグを`tax-services`に変更
2. `/services/`を税理士事務所専用のページにする
3. 税理士サービスは`/tax-services/`で表示

### オプション3: Ajax ページネーション

1. JavaScriptでページネーションを実装
2. Ajaxでデータを取得して表示
3. URLを変更せずにページを切り替え

## 推奨アプローチ

**オプション1**を推奨します。理由：

- 固定ページなので柔軟にカスタマイズ可能
- ページネーションが正しく動作する
- SEO的にも問題なし
- 実装が簡単

## 実装手順

1. `archive-tax_service.php`を`page-services.php`にコピー
2. テンプレート名を変更:
   ```php
   /**
    * Template Name: サービスページ
    */
   ```
3. WordPressで固定ページ「サービス」を作成
4. スラッグを`services`に設定
5. テンプレートを「サービスページ」に選択
6. 保存して確認

## 注意事項

- 固定ページに変更すると、`get_query_var('paged')`は使用できません
- `$_GET['paged']`または`get_query_var('page')`を使用する必要があります
- パーマリンク設定を更新する必要があります

## 作成日

2026-01-20
