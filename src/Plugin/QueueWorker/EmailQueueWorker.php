<?php

declare(strict_types=1);

namespace Drupal\nsb_plugin_examples\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\Attribute\QueueWorker;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Mail\MailManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a queue worker example that sends emails.
 */
#[QueueWorker(
  id: "nsb_custom_queue_worker",
  title: new TranslatableMarkup("Custom queue worker"),
  cron: ["time" => 60]
)]
class CustomQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The mail manager.
   */
  protected MailManagerInterface $mailManager;

  /**
   * The logger.
   */
  protected LoggerInterface $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition,
    MailManagerInterface $mail_manager,
    LoggerInterface $logger
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->mailManager = $mail_manager;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.mail'),
      $container->get('logger.channel.nsb_plugin_examples'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem(mixed $data): void {
    if (!is_array($data)) {
      return;
    }

    if (empty($data['email']) || !is_string($data['email'])) {
      return;
    }

    $result = $this->mailManager->mail(
      'nsb_plugin_examples',
      'example',
      $data['email'],
      'en',
      [
        'message' => 'Hello from QueueWorker',
      ],
    );

    if (empty($result['result'])) {
      $this->logger->warning('Queue worker could not send an email to %email.', [
        '%email' => $data['email'],
      ]);
    }
  }

}
