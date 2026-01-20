<?php
if (!defined('ABSPATH')) exit;

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_tax_service_details',
        'title' => '税理士サービス詳細情報',
        'fields' => array(
            array('key' => 'field_tab_basic', 'label' => '基本情報', 'type' => 'tab'),
            array('key' => 'field_office_name', 'label' => '事務所名', 'name' => 'office_name', 'type' => 'text', 'required' => 1),
            array('key' => 'field_representative', 'label' => '代表者名', 'name' => 'representative', 'type' => 'text'),
            array('key' => 'field_address', 'label' => '所在地', 'name' => 'address', 'type' => 'textarea', 'rows' => 2),
            array('key' => 'field_tab_taxonomy', 'label' => '分類（業種・課題・エリア）', 'type' => 'tab'),
            array('key' => 'field_industries', 'label' => '対応業種', 'name' => 'industries', 'type' => 'taxonomy', 'taxonomy' => 'industry', 'field_type' => 'checkbox', 'add_term' => 1, 'save_terms' => 1, 'load_terms' => 1),
            array('key' => 'field_challenges', 'label' => '対応課題', 'name' => 'challenges', 'type' => 'taxonomy', 'taxonomy' => 'challenge', 'field_type' => 'checkbox', 'add_term' => 1, 'save_terms' => 1, 'load_terms' => 1),
            array('key' => 'field_areas', 'label' => '対応エリア', 'name' => 'areas', 'type' => 'taxonomy', 'taxonomy' => 'area', 'field_type' => 'checkbox', 'add_term' => 1, 'save_terms' => 1, 'load_terms' => 1),
            array('key' => 'field_tab_contact', 'label' => '連絡先情報', 'type' => 'tab'),
            array('key' => 'field_email', 'label' => 'メールアドレス', 'name' => 'email', 'type' => 'email'),
            array('key' => 'field_website', 'label' => '公式サイト', 'name' => 'website', 'type' => 'url'),
            array('key' => 'field_tab_display', 'label' => '掲載設定', 'type' => 'tab'),
            array('key' => 'field_listing_status', 'label' => '掲載ステータス', 'name' => 'listing_status', 'type' => 'radio', 'choices' => array('active' => '掲載中', 'inactive' => '掲載停止'), 'default_value' => 'active', 'required' => 1),
        ),
        'location' => array(array(array('param' => 'post_type', 'operator' => '==', 'value' => 'tax_service'))),
        'position' => 'normal',
    ));
}
