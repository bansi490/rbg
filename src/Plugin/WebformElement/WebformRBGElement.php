<?php

namespace Drupal\rbg\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElement\WebformAutocomplete;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Provides a 'Webform_rbgelement' element.
 *
 * @WebformElement(
 *   id = "Webform_rbgelement",
 *   label = @Translation("Webform RBG Element"),
 *   description = @Translation("Provides a form element for input of a single-line text."),
 *   category = @Translation("RBG elements"),
 * )
 */
class WebformRBGElement extends WebformAutocomplete {

  /**
   * {@inheritdoc}
   */
  protected function defineDefaultProperties() {
    return [
      // Form display.
      'input_mask' => '',
      'input_hide' => FALSE,
      // Form validation.
      'counter_type' => '',
      'counter_minimum' => NULL,
      'counter_minimum_message' => '',
      'counter_maximum' => NULL,
      'counter_maximum_message' => '',
    ] + parent::defineDefaultProperties() + $this->defineDefaultMultipleProperties();
  }

  /* ************************************************************************ */

  /**
   * {@inheritdoc}
   */
  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
    if (!array_key_exists('#maxlength', $element)) {
      $element['#maxlength'] = 255;
    }
    parent::prepare($element, $webform_submission);
  }

}
