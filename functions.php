<?php
// Register Custom Post Type
add_action('init', function () {
  register_post_type('tax_service', [
    'label' => '税理士サービス',
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'services'],
    'supports' => ['title','editor','thumbnail'],
  ]);
});

// Register Taxonomies
add_action('init', function () {
  $taxonomies = [
    'service_industry' => '業種',
    'service_issue' => '課題',
    'service_phase' => 'フェーズ',
    'service_area' => '対応エリア'
  ];
  foreach ($taxonomies as $slug => $label) {
    register_taxonomy($slug, 'tax_service', [
      'label' => $label,
      'public' => true,
      'rewrite' => ['slug' => $slug],
      'hierarchical' => true,
    ]);
  }
});
