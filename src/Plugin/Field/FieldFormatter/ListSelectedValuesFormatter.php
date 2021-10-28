<?php

namespace Drupal\list_selected_values\Plugin\Field\FieldFormatter;

// These taken from OptionsDefaultFormatter.php
use Drupal\Core\Field\FieldFilteredMarkup;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\OptGroup;

/**
 * Plugin implementation of the 'list_selected_values_formatter' formatter.
 * Most of this is taken from OptionsDefaultFormatter.php.
 *
 * @FieldFormatter(
 *   id = "list_selected_values_formatter",
 *   label = @Translation("List selected values"),
 *   field_types = {
 *     "list_integer",
 *     "list_float",
 *     "list_string",
 *   }
 * )
 */
class ListSelectedValuesFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    // Only collect allowed options if there are actually items to display.
    if ($items->count()) {
      $provider = $items->getFieldDefinition()
        ->getFieldStorageDefinition()
        ->getOptionsProvider('value', $items->getEntity());
      // Get all of the options, flatten the possible options, to support opt groups.
      $options = OptGroup::flattenOptions($provider->getPossibleOptions());

      // Need a new array of all the set values for this field to compare against $options.
      $set_values = [];
      foreach ($items as $delta => $item) {
        $value = $item->value;
        $set_values[$item->value] = TRUE;
      }

      // I'm guessing that delta needs to be numeric so...
      $i = 0;
      foreach ($options as $key => $value) {
        $selected = FALSE;
        if (array_key_exists($key, $set_values)) {
          $selected = TRUE;
        }
        // If the stored value is in the current set of allowed values, display
        // the associated label, otherwise just display the raw value.
        $output = isset($options[$key]) ? $options[$key] : $key;
        $elements[$i] = [
          '#theme' => 'list_selected_values',
          '#item' => $output,
          '#selected' => $selected,
          '#allowed_tags' => FieldFilteredMarkup::allowedTags(),
        ];
        $i++;
      }
    }

    return $elements;
  }
}
