<?php

namespace Drupal\pagedesigner_view_modes_display;

use Drupal\Component\Serialization\Json;
use Drupal\pagedesigner\ElementViewBuilder as PagedesignerElementViewBuilder;
use Drupal\pagedesigner\Entity\Element;

/**
 * Class ElementViewBuilder.
 *
 * Overrides the view builder for the pagedesigner_element entity type.
 *
 * @package Drupal\pagedesigner_view_modes_display
 */
class ElementViewBuilder extends PagedesignerElementViewBuilder {

  use ViewModesTrait;

  /**
   * {@inheritDoc}
   */
  public function getSpecialViewModes() {
    $available_view_modes = $this->getViewModesOptions();
    $view_modes = array_merge(parent::getSpecialViewModes(), array_keys($available_view_modes));
    return $view_modes;
  }

  /**
   * Adds the default lazy builder for an element.
   *
   * @param \Drupal\pagedesigner\Entity\Element $entity
   *   The element.
   * @param string $view_mode
   *   The view mode.
   * @param array $build
   *   The build arry.
   */
  public function addLazyBuilder(Element $entity, string $view_mode, array &$build = []) {
    $arguments = ['#entity_id' => $entity->id(), '#view_mode' => $view_mode];
    if (isset($build['#cache'])) {
      $arguments['#cache'] = $build['#cache'];
    }
    // The render array can only have some properties.
    // Keeping the weight to retain ordering.
    $build = [
      '#weight' => $build['#weight'] ?? 0,
    ];
    // Add the lazy builder to the render array.
    $build['#lazy_builder'] = ['\Drupal\pagedesigner_view_modes_display\ElementViewBuilder::lazyBuilder', [Json::encode($arguments)]];
  }

  /**
   * Lazy builder callback to execute build method on handlers.
   *
   * @param string $args
   *   The serialized arguments
   *   containing #entity_id, #view_mode, #cache (optional).
   *
   * @return array
   *   The render array.
   */
  public static function lazyBuilder(string $args) {
    $arguments = Json::decode($args);
    $entity = Element::load($arguments['#entity_id']);
    $view_mode = $arguments['#view_mode'];
    if ($entity->hasField('field_hidden_view_modes') && !$entity->field_hidden_view_modes->isEmpty()) {
      $hidden_view_modes = Json::decode((string) $entity->field_hidden_view_modes->value);
      if (!empty($hidden_view_modes) && in_array($view_mode, $hidden_view_modes)) {
        return [];
      }
    }
    return parent::lazyBuilder($args);
  }

}
