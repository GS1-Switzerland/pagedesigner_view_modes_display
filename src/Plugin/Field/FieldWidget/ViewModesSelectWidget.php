<?php

namespace Drupal\pagedesigner_view_modes_display\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\pagedesigner_view_modes_display\ViewModeTrait;

/**
 * Plugin implementation of the 'view_modes_select_widget' widget.
 *
 * @FieldWidget(
 *   id = "view_modes_select_widget",
 *   label = @Translation("View Modes Select Widget"),
 *   field_types = {
 *     "view_modes_select"
 *   }
 * )
 */
class ViewModesSelectWidget extends WidgetBase {

  use ViewModeTrait;

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $view_modes = $this->getViewModeOptions();

    $element['value'] = [
      '#type' => 'select',
      '#title' => $this->t('View Modes'),
      '#options' => $view_modes,
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : [],
      '#multiple' => TRUE,
    ];

    return $element;
  }
}