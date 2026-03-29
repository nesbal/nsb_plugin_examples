<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a number formatter with customizable separators.
 */
#[FieldFormatter(
  id: "nsb_number",
  label: new TranslatableMarkup("Number Formatter"),
  field_types: ["decimal", "float", "integer"]
)]
class NumberFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'decimals' => 2,
      'decimal_separator' => '.',
      'thousands_separator' => ',',
      'trim_trailing_zeros' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    return [
      'decimals' => [
        '#type' => 'number',
        '#title' => $this->t('Decimals'),
        '#default_value' => $this->getSetting('decimals'),
        '#min' => 0,
      ],
      'decimal_separator' => [
        '#type' => 'textfield',
        '#title' => $this->t('Decimal separator'),
        '#default_value' => $this->getSetting('decimal_separator'),
        '#size' => 2,
        '#maxlength' => 1,
      ],
      'thousands_separator' => [
        '#type' => 'textfield',
        '#title' => $this->t('Thousands separator'),
        '#default_value' => $this->getSetting('thousands_separator'),
        '#size' => 2,
        '#maxlength' => 1,
      ],
      'trim_trailing_zeros' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Remove trailing zeros'),
        '#default_value' => $this->getSetting('trim_trailing_zeros'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Decimals: @decimals', [
        '@decimals' => $this->getSetting('decimals'),
      ]),
      $this->t('Decimal: "@decimal_separator"', [
        '@decimal_separator' => $this->getSetting('decimal_separator'),
      ]),
      $this->t('Thousands: "@thousands_separator"', [
        '@thousands_separator' => $this->getSetting('thousands_separator'),
      ]),
      $this->t('Trim trailing zeros: @trim', [
        '@trim' => $this->getSetting('trim_trailing_zeros') ? 'Yes' : 'No',
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsValidate(array &$form, FormStateInterface $form_state): void {
    foreach (['decimal_separator', 'thousands_separator'] as $key) {
      $value = (string) $form_state->getValue($key);

      if (strlen($value) !== 1) {
        $form_state->setErrorByName($key, $this->t('Separator must be exactly one character.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    $decimals = (int) $this->getSetting('decimals');
    $decimal_separator = $this->getSetting('decimal_separator');
    $thousands_separator = $this->getSetting('thousands_separator');
    $trim = (bool) $this->getSetting('trim_trailing_zeros');

    foreach ($items as $delta => $item) {
      $value = number_format(
        (float) $item->value,
        $decimals,
        $decimal_separator,
        $thousands_separator
      );

      if ($trim && $decimals > 0) {
        $ending = $decimal_separator . str_repeat('0', $decimals);

        if (str_ends_with($value, $ending)) {
          $value = substr($value, 0, -strlen($ending));
        }
      }

      $elements[$delta] = [
        '#markup' => Html::escape($value),
      ];
    }

    return $elements;
  }

}
