# NSB Plugin Examples

A collection of small Drupal plugin examples.

## What it shows
- **Block plugin**: Simple custom block rendering static content.
- **FieldFormatter plugin**: Formats numeric field values with custom separators and optional trailing zero removal.
- **FieldWidget plugin**: Custom input widget that normalizes decimal values (e.g. comma to dot) and validates against field precision.
- **Condition plugin**: Checks if the current user has logged in within a configurable number of days. (see [usage](#condition-plugin-usage))
- **Views field plugin**: Displays a "New" badge if the node was created within a given number of days. (see [setup](#views-field-plugin-setup))
- **Views filter plugin**: Filters content created within the last X days. (see [setup](#views-filter-plugin-setup))
- **QueueWorker plugin**: Sends email notifications asynchronously via Drupal Queue API. (see [usage](#queue-worker-usage))
## Condition plugin usage

Example usage inside a custom class (e.g. a block plugin):

```php
$condition = $this->conditionManager->createInstance('nsb_recent_login', [
  'days' => 7,
]);

if ($condition->evaluate()) {
  // Condition passed.
}
```
## Views field plugin setup

This plugin is a computed field (not stored in the database), so it must be attached to a base table via `hook_views_data()`.

### `nsb_plugin_examples.views.inc`
```php
<?php

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

  return $data;
}
```

## Views filter plugin setup

This plugin filters content based on creation date and must be attached to a base table via `hook_views_data()`.

### `nsb_plugin_examples.views.inc`

```php
$data['node_field_data']['nsb_new_content_filter'] = [
  'title' => t('New content (last X days)'),
  'help' => t('Filters content created within the last X days'),
  'filter' => [
    'id' => 'nsb_new_content_filter',
  ],
];
```

### Queue Worker usage

Add an item to the queue:

```php
\Drupal::queue('nsb_email_queue_worker')->createItem([
  'email' => 'test@example.com',
  'langcode' => 'en',
]);
```

The queue is processed during Drupal cron runs:

`drush cron`

You can also run it manually:

`drush queue:run nsb_email_queue_worker`
