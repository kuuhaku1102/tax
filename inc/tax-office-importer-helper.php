<?php
// タームを取得または作成するヘルパー関数（改善版）
function get_or_create_term($term_name, $taxonomy) {
    if (empty($term_name)) {
        return null;
    }
    
    // 文字列に変換してトリム
    $term_name = trim((string)$term_name);
    
    if (empty($term_name)) {
        return null;
    }
    
    // 既存のタームを検索（名前で検索）
    $existing_term = get_term_by('name', $term_name, $taxonomy);
    
    if ($existing_term && !is_wp_error($existing_term)) {
        return array(
            'term_id' => $existing_term->term_id,
            'term_taxonomy_id' => $existing_term->term_taxonomy_id
        );
    }
    
    // 新規作成（名前とスラッグを明示的に指定）
    $term = wp_insert_term(
        $term_name,
        $taxonomy,
        array(
            'slug' => sanitize_title($term_name)
        )
    );
    
    if (is_wp_error($term)) {
        // エラーログを記録
        error_log('Failed to create term: ' . $term_name . ' in ' . $taxonomy . ': ' . $term->get_error_message());
        return null;
    }
    
    return is_array($term) ? $term : null;
}
