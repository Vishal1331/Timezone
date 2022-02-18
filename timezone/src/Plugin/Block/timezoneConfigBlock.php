<?php

namespace Drupal\timezone\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\timezone\CurrentTimeByTimezone;


/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "timezone_config_block",
 *   admin_label = @Translation("timezone Config Block"),
 * )
 */
class timezoneConfigBlock extends BlockBase implements ContainerFactoryPluginInterface {
   /**
    * @var CurrentTimeByTimezone $currentTime.
    * @var ConfigFactory $configFactory.
    */
   protected $currentTime;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param Drupal\timezone\CurrentTimeByTimezone $currentTime;
   * @param Drupal\Core\Config\ConfigFactory $configFactory;
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentTimeByTimezone $currentTime, ConfigFactory $configFactory ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentTime = $currentTime;
    $this->configFactory = $configFactory;
  }
  
  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('timezone.get_current_time_by_timezone'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
  
    return [
      '#theme' => 'timezone',
      '#country' => $this->configFactory->getEditable('config.timezone_config_form')->get('timezone_config_country'),
      '#city' => $this->configFactory->getEditable('config.timezone_config_form')->get('timezone_config_city'),
      '#date_time' => $this->currentTime->getTimeWithTimezone(),
      '#cache' => [
        'tags' => ['custom_timezone_config_tag'],
      ],
    ];
  }

}