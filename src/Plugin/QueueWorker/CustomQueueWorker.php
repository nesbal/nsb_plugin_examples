<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\QueueWorker;

use Drupal\Core\Queue\Attribute\QueueWorker;
use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Provides a simple Queue Worker example.
 */
#[QueueWorker(
  id: "custom_queue_worker",
  title: new \Drupal\Core\StringTranslation\TranslatableMarkup("Custom Queue Worker"),
  cron: ["time" => 60]
)]
class CustomQueueWorker extends QueueWorkerBase {

  /**
   * Processes a single queue item.
   */
  public function processItem(mixed $data): void {
    // Nothing for now.
  }

}
