<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\views\filter;

use Drupal\views\Attribute\ViewsFilter;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Filters content created within the last X days.
 */
#[ViewsFilter("nsb_new_content_filter")]
class NewContentFilter extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();

    $options['days'] = ['default' => 7];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    $form['days'] = [
      '#type' => 'number',
      '#title' => $this->t('Days'),
      '#default_value' => $this->options['days'],
      '#min' => 0,
      '#description' => $this->t('Show content created within the last X days.'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query(): void {
    $days = (int) $this->options['days'];

    $timestamp = \Drupal::time()->getCurrentTime() - ($days * 86400);

    $this->query->addWhereExpression(
      $this->options['group'],
      'node_field_data.created >= :timestamp',
      [':timestamp' => $timestamp]
    );
  }

}
