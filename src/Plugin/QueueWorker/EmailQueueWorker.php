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
 * Provides an email notification queue worker.
 */
#[QueueWorker(
  id: "nsb_email_queue_worker",
  title: new TranslatableMarkup("Email notifications"),
  cron: ["time" => 60]
)]
class EmailQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

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
    LoggerInterface $logger,
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
      'notification',
      $data['email'],
      $data['langcode'] ?? 'en',
      [
        'subject' => $data['subject'] ?? 'Notification',
        'message' => $data['message'] ?? 'You have a new notification.',
      ],
    );

    if (empty($result['result'])) {
      $this->logger->warning('Queue worker could not send an email to %email.', [
        '%email' => $data['email'],
      ]);
    }
  }

}
