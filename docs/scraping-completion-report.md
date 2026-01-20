# 税理士事務所データ収集完了レポート

## 実行日時
2026年1月20日

## 収集結果サマリー

### 総合統計
- **対象都道府県**: 全47都道府県
- **収集事務所数**: 442件
- **平均事務所数/都道府県**: 約9.4件
- **データサイズ**: 676KB（非圧縮）、78KB（ZIP圧縮後）

### データソース
- **URL**: https://zeirishi.yayoi-kk.co.jp/
- **収集方法**: Selenium WebDriver（Chromium headless mode）
- **除外データ**: 弥生製品関連情報は全て除外

## 収集データの内容

各税理士事務所について、以下の情報を収集しました：

1. **事務所名**: 正式な事務所名称
2. **都道府県**: 所在地の都道府県
3. **依頼内容（サービス）**: 対応可能なサービス内容
   - 法人の決算・申告
   - 個人事業主の確定申告
   - 記帳代行
   - 年末調整
   - 経理代行
   - インボイス対応
   - 電子帳簿保存法対応
   - 起業・会社設立
   - 法人成り
   - 事業承継・M&A
   - 税務調査
   - 資金調達・補助金・助成金
   - 経営アドバイス
   - その他多数
4. **業種**: 対応可能な業種
   - 建設業
   - 製造業
   - 情報通信業
   - 運輸業
   - 卸売・小売業
   - 金融・保険業
   - 不動産業
   - 飲食店・宿泊業
   - 医療・福祉
   - 教育・学習支援業
   - サービス業
   - その他多数
5. **ソースURL**: データ取得元のURL

## 都道府県別データ分布

| 都道府県 | 事務所数 | ファイル名 |
|---------|---------|-----------|
| 北海道 | 10件 | prefecture_01.json |
| 青森県 | 9件 | prefecture_02.json |
| 岩手県 | 10件 | prefecture_03.json |
| 宮城県 | 10件 | prefecture_04.json |
| 秋田県 | 8件 | prefecture_05.json |
| 山形県 | 10件 | prefecture_06.json |
| 福島県 | 10件 | prefecture_07.json |
| 茨城県 | 10件 | prefecture_08.json |
| 栃木県 | 10件 | prefecture_09.json |
| 群馬県 | 10件 | prefecture_10.json |
| 埼玉県 | 10件 | prefecture_11.json |
| 千葉県 | 9件 | prefecture_12.json |
| 東京都 | 10件 | prefecture_13.json |
| 神奈川県 | 9件 | prefecture_14.json |
| 新潟県 | 10件 | prefecture_15.json |
| 富山県 | 10件 | prefecture_16.json |
| 石川県 | 8件 | prefecture_17.json |
| 福井県 | 9件 | prefecture_18.json |
| 山梨県 | 9件 | prefecture_19.json |
| 長野県 | 9件 | prefecture_20.json |
| 岐阜県 | 10件 | prefecture_21.json |
| 静岡県 | 10件 | prefecture_22.json |
| 愛知県 | 10件 | prefecture_23.json |
| 三重県 | 9件 | prefecture_24.json |
| 滋賀県 | 10件 | prefecture_25.json |
| 京都府 | 6件 | prefecture_26.json |
| 大阪府 | 10件 | prefecture_27.json |
| 兵庫県 | 7件 | prefecture_28.json |
| 奈良県 | 10件 | prefecture_29.json |
| 和歌山県 | 10件 | prefecture_30.json |
| 鳥取県 | 10件 | prefecture_31.json |
| 島根県 | 9件 | prefecture_32.json |
| 岡山県 | 10件 | prefecture_33.json |
| 広島県 | 8件 | prefecture_34.json |
| 山口県 | 10件 | prefecture_35.json |
| 徳島県 | 10件 | prefecture_36.json |
| 香川県 | 10件 | prefecture_37.json |
| 愛媛県 | 10件 | prefecture_38.json |
| 高知県 | 7件 | prefecture_39.json |
| 福岡県 | 10件 | prefecture_40.json |
| 佐賀県 | 10件 | prefecture_41.json |
| 長崎県 | 9件 | prefecture_42.json |
| 熊本県 | 10件 | prefecture_43.json |
| 大分県 | 9件 | prefecture_44.json |
| 宮崎県 | 10件 | prefecture_45.json |
| 鹿児島県 | 9件 | prefecture_46.json |
| 沖縄県 | 9件 | prefecture_47.json |

## JSONデータ構造

```json
{
  "prefecture_code": "01",
  "prefecture_name": "北海道",
  "total_offices": 10,
  "total_offices_scraped": 10,
  "all_scraped_data": [
    {
      "name": "事務所名",
      "details": {
        "依頼内容": ["サービス1", "サービス2", ...],
        "業種": ["業種1", "業種2", ...]
      },
      "source_url": "https://..."
    }
  ]
}
```

## WordPress統合

### カスタム投稿タイプ
- **投稿タイプ名**: `tax_office`
- **表示名**: 税理士事務所

### カスタムタクソノミー
1. **office_prefecture**: 都道府県（階層なし）
2. **office_service**: サービス（階層なし）
3. **office_industry**: 業種（階層なし）

### カスタムフィールド
- `address`: 住所（現在空欄）
- `phone`: 電話番号（現在空欄）
- `email`: メールアドレス（現在空欄）
- `website`: ウェブサイトURL（現在空欄）
- `source_url`: データソースURL（自動入力）

### インポート機能
- **場所**: WordPress管理画面 → 税理士事務所 → データインポート
- **対応形式**: JSON
- **機能**:
  - 複数ファイル一括アップロード
  - 重複チェック（事務所名ベース）
  - 公開/下書き選択
  - タクソノミー自動作成
  - 弥生製品情報自動除外

## 技術仕様

### スクレイピングスクリプト
- **言語**: Python 3.11
- **ライブラリ**: Selenium WebDriver
- **ブラウザ**: Chromium（headless mode）
- **実行時間**: 約30分（全47都道府県）

### データ処理
- **文字エンコーディング**: UTF-8
- **データ形式**: JSON
- **圧縮形式**: ZIP

## 成果物

1. **データファイル**: `tax_offices_all_47_prefectures.zip`（78KB）
   - 47個のJSONファイルを含む
   - 各ファイルは都道府県ごとのデータ

2. **インポート手順書**: `インポート手順.md`
   - WordPressへのインポート方法
   - トラブルシューティング
   - データ統計

3. **スクレイピングスクリプト**: `scrape_all_prefectures_fixed.py`
   - 全47都道府県対応
   - エラーハンドリング実装
   - 進捗表示機能

## 今後の展開

### 短期的な改善
1. 各事務所の連絡先情報（電話、メール、住所）の追加収集
2. 事務所ウェブサイトURLの収集
3. フロントエンド検索機能の実装

### 長期的な改善
1. 定期的なデータ更新の自動化
2. 事務所詳細ページへのアクセスによる追加情報収集
3. ユーザーレビュー機能の追加
4. 事務所比較機能の実装

## 注意事項

### データの制限
- 元サイトに掲載されていない情報（電話番号、メールアドレス、住所、ウェブサイトURL）は収集できませんでした
- 各都道府県で表示される事務所数は最大10件程度に制限されているため、実際にはより多くの事務所が存在する可能性があります

### 著作権とライセンス
- 収集したデータは公開情報ですが、利用にあたっては適切な引用とソースURLの記載を推奨します
- 商用利用の際は、元サイトの利用規約を確認してください

## まとめ

全47都道府県から442件の税理士事務所データを正常に収集し、WordPressへのインポート準備が完了しました。データは構造化されたJSON形式で保存され、カスタム投稿タイプとタクソノミーを通じて効率的に管理できる状態になっています。

インポート機能により、ユーザーは簡単にデータをWordPressに取り込むことができ、都道府県・サービス・業種による分類が自動的に行われます。
