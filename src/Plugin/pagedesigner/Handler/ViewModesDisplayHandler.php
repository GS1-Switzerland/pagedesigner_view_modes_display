<?php

namespace Drupal\pagedesigner_view_modes_display\Plugin\pagedesigner\Handler;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\pagedesigner\Entity\Element;
use Drupal\pagedesigner\Plugin\HandlerConfigTrait;
use Drupal\pagedesigner\Plugin\HandlerPluginBase;
use Drupal\pagedesigner\Plugin\HandlerUserTrait;
use Drupal\pagedesigner_view_modes_display\ViewModesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Add view mode display functionality to "row" and "component" patterns.
 *
 * @PagedesignerHandler(
 *   id = "view_modes_display",
 *   name = @Translation("View modes display handler"),
 *   types = {
 *      "row",
 *      "component",
 *      "block",
 *   },
 *   weight = 50
 * )
 */
class ViewModesDisplayHandler extends HandlerPluginBase {

  // Import config property and setter.
  use HandlerConfigTrait;
  // Import user property and setter.
  use HandlerUserTrait;
  use StringTranslationTrait;
  use ViewModesTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->setConfigFactory($container->get('config.factory'));
    $instance->setCurrentUser($container->get('current_user'));
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build(Element $entity, string $view_mode, array &$build = []) {
    // Ensure pagedesigner settings are updated.
    $cache_metadata = new CacheableMetadata();
    $cache_metadata->setCacheTags(['config:pagedesigner_view_modes_display.settings']);
    $cache_metadata->applyTo($build);
  }

  /**
   * {@inheritDoc}
   */
  public function collectAttachments(array &$attachments) {
    $attachments['drupalSettings']['pagedesigner_view_modes_display']['view_modes'] = $this->getViewModesOptions();
    $attachments['library'][] = 'pagedesigner_view_modes_display/pagedesigner';
  }

  /**
   * {@inheritDoc}
   */
  public function serialize(Element $entity, array &$result = []) {
    if ($entity->hasField('field_hidden_view_modes') && !$entity->field_hidden_view_modes->isEmpty()) {
      $result['hidden_view_modes'] = Json::decode((string) $entity->field_hidden_view_modes->value);
    }
    else {
      $result['hidden_view_modes'] = [];
    }
  }

  /**
   * {@inheritDoc}
   */
  public function patch(Element $entity, array $data) {
    if ($entity->hasField('field_hidden_view_modes')) {
      if (!empty($data['hidden_view_modes'])) {
        $entity->field_hidden_view_modes->value = Json::encode($data['hidden_view_modes']);
      }
      else {
        $entity->field_hidden_view_modes->value = '';
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function view(Element $entity, string $view_mode, array &$build = []) {
    // Hide if target entity view mode is in the hidden view modes list.
    if ($entity->hasField('field_hidden_view_modes') && !$entity->field_hidden_view_modes->isEmpty()) {
      $hidden_view_modes = Json::decode((string) $entity->field_hidden_view_modes->value);
      if (!empty($hidden_view_modes) && in_array($view_mode, $hidden_view_modes)) {
        $build['#access'] = FALSE;
      }
    }
  }

}
