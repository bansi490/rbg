<?php

namespace Drupal\rbg_schoolyearcheck\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provide block for the schoolyear check lookup
 *
 * @Block(
 *    id = "schoolyearcheck_block",
 *    admin_label = @Translation("School year check lookup")
 * )
 */
class rbgSchoolyearcheckBlock extends BlockBase {

  /**
   * Render webform, which is inside the /src/Form directory
   */
  public function build()
  {
    $form = \Drupal::formBuilder()->getForm('Drupal\rbg_schoolyearcheck\Form\rbgschoolyearcheckForm');
    $renderForm = \Drupal::service('renderer')->render($form);

    return [
      '#type'=>'markup',
      '#markup'=>$renderForm
    ];
  }

}
