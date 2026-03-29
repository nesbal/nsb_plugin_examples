# NSB Plugin Examples

A collection of small Drupal plugin examples.

## What it shows
- **Block plugin**: Simple custom block rendering static content.
- **FieldFormatter plugin**: Formats numeric field values with custom separators and optional trailing zero removal.
- **FieldWidget plugin**: Custom input widget that normalizes decimal values (e.g. comma to dot) and validates against field precision.
- **Condition plugin**: Checks if the current user has logged in within a configurable number of days.

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
