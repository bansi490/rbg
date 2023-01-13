<?php

namespace Drupal\rbg\Element;

use Drupal\webform\Element\WebformAutocomplete;

/**
 * Provides a one-line text field with webform RBG element.
 *
 * @FormElement("Webform_rbgelement")
 */
class WebformRBGElement extends WebformAutocomplete {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);

    $info = parent::getInfo();
    $info['#pre_render'][] = [$class, 'preRenderWebformRBGElement'];
    return $info;
  }

  /**
   * Prepares a #type 'webform_rbgelement' render element for input.html.twig.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   *   Properties used: #title, #value, #description, #size, #maxlength,
   *   #placeholder, #required, #attributes.
   *
   * @return array
   *   The $element with prepared variables ready for input.html.twig.
   */
  public static function preRenderWebformRBGElement($element) {
    static::setAttributes($element, ['webform-rbgelement']);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineTranslatableProperties() {
    return array_merge(parent::defineTranslatableProperties(), ['rbgelement_items']);
  }

}
