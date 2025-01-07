<?php

namespace Drupal\pagedesigner_view_modes_display\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'view_modes_select' field type.
 *
 * @FieldType(
 *   id = "view_modes_select",
 *   label = @Translation("View Modes Select"),
 *   description = @Translation("A field to select multiple view modes."),
 *   default_widget = "view_modes_select_widget",
 *   default_formatter = "list_default"
 * )
 */
class ViewModesSelect extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('View Mode'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'text',
          'size' => 'big',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    parent::preSave();
    // Ensure the value is stored as a serialized array.
    $this->set('value', serialize($this->value));
  }

  /**
   * {@inheritdoc}
   */
  public function postLoad() {
    parent::postLoad();
    // Ensure the value is unserialized when loaded.
    $this->value = unserialize($this->value);
  }

}