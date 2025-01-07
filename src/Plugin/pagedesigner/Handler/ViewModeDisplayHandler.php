<?php

namespace Drupal\pagedesigner_view_modes_display\Plugin\pagedesigner\Handler;

use Drupal\Component\Serialization\Json;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\pagedesigner\Entity\Element;
use Drupal\pagedesigner\Plugin\HandlerConfigTrait;
use Drupal\pagedesigner\Plugin\HandlerPluginBase;
use Drupal\pagedesigner\Plugin\HandlerUserTrait;
use Drupal\pagedesigner_view_modes_display\ViewModeTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Add view mode display functionality to "row" and "component" patterns.
 *
 * @PagedesignerHandler(
 *   id = "view_mode_display",
 *   name = @Translation("View mode displays handler"),
 *   types = {
 *      "row",
 *      "component"
 *   },
 *   weight = 50
 * )
 */
class ViewModeDisplayHandler extends HandlerPluginBase {

  // Import config property and setter.
  use HandlerConfigTrait;
  // Import user property and setter.
  use HandlerUserTrait;
  use StringTranslationTrait;
  use ViewModeTrait;

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
   * {@inheritDoc}
   */
  public function collectAttachments(array &$attachments) {
    $attachments['drupalSettings']['pagedesigner_view_modes_display']['view_modes'] = $this->getViewModeOptions();
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
    if (!$entity->field_hidden_view_modes->isEmpty()) {
      $hidden_view_modes_array = Json::decode((string) $entity->field_hidden_view_modes->value);
      if (empty($hidden_view_modes_array)) {
        return;
      }
      $hidden_view_modes = [];
      foreach ($hidden_view_modes_array as $hidden_view_mode) {
        foreach ($hidden_view_mode as $key => $value) {
          $hidden_view_modes[] = $key;
        }
      }
      if (in_array($view_mode, $hidden_view_modes)) {
        $build['#access'] = FALSE;
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function build(Element $entity, string $view_mode, array &$build = []) {
    //$this->addViewModesDisplay($entity, $build);
  }

  /**
   * Add the user generated effects of the entity to drupalSettings.
   *
   * @param \Drupal\pagedesigner\Entity\Element $entity
   *   The entity being rendered.
   * @param array $build
   *   The render array.
   */
  protected function addViewModesDisplay(Element $entity, array &$build = []) {
    if ($entity->hasField('field_hidden_view_modes')) {
      if (empty($build['#attached'])) {
        $build['#attached'] = [];
      }
      if (empty($build['#attached']['drupalSettings'])) {
        $build['#attached']['drupalSettings'] = [];
      }
      if (empty($build['#attached']['drupalSettings']['pagedesigner'])) {
        $build['#attached']['drupalSettings']['pagedesigner'] = [];
      }
      if (empty($build['#attached']['drupalSettings']['pagedesigner']['hidden_view_modes'])) {
        $build['#attached']['drupalSettings']['pagedesigner']['hidden_view_modes'] = [];
      }
      $build['#attached']['drupalSettings']['pagedesigner']['hidden_view_modes']['#pd-cp-' . $entity->id()] = Json::decode((string) $entity->field_hidden_view_modes->value);
    }
  }

}
