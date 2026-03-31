<?php

/**
 * @file
 * Defines Views data for custom fields and filters.
 */

/**
 * Implements hook_views_data().
 */
function nsb_plugin_examples_views_data(): array {
  $data = [];

  $data['node_field_data']['nsb_new_badge'] = [
    'title' => t('New badge'),
    'help' => t('Displays a "New" badge for recent content'),
    'field' => [
      'id' => 'nsb_new_badge',
      'click sortable' => FALSE,
    ],
  ];

  $data['node_field_data']['nsb_new_content_filter'] = [
    'title' => t('New content (last X days)'),
    'help' => t('Filters content created within the last X days'),
    'filter' => [
      'id' => 'nsb_new_content_filter',
    ],
  ];

  return $data;
}
