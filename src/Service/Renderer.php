<?php

namespace Drupal\pagedesigner_view_modes_display\Service;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\pagedesigner\Entity\Element;
use Drupal\pagedesigner\Service\Renderer as PagedesignerRenderer;

/**
 * Renderer service for pagedesigner view mode display.
 */
class Renderer extends PagedesignerRenderer {

  /**
   * Render for specific view mode.
   *
   * @param \Drupal\pagedesigner\Entity\Element $container
   *   The container element.
   * @param \Drupal\Core\Entity\ContentEntityBase $entity
   *   The entity.
   * @param string $view_mode
   *   The view mode.
   *
   * @return \Drupal\pagedesigner\Service\Renderer
   *   The renderer service.
   */
  public function renderForViewMode(Element $container, ?ContentEntityBase $entity = NULL, $view_mode) {

    if ($container == NULL) {
      return $this;
    }
    if (empty($this->output)) {
      $this->output = [];
    }
    if (!$this->rendering) {
      self::$styles = NULL;
    }
    if ($entity) {
      $this->preload($entity, TRUE);
    }
    $this->rendering = TRUE;
    $view_builder = $this->entityTypeManager->getViewBuilder('pagedesigner_element');
    $this->output = $view_builder->view($container, $view_mode);
    $this->rendering = FALSE;
    $this->addStyles($container);
    return $this;
  }

}
