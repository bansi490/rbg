<?php

namespace Drupal\rbg_streetspace\Plugin\WebformHandler;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Markup;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\RequestStack;

/**
  * Webform validate handler.
  *
  * @WebformHandler(
  *   id = "custom_get_remote_ip",
  *   label = @Translation("Get remote ID"),
  *   category = @Translation("Settings"),
  *   description = @Translation("Get remote IP address."),
  *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
  *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
  *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
  * )
  */

class rbgWebformHandler extends WebformHandlerBase {

    use StringTranslationTrait;

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
      $this->validateIPAddress($form_state);

    }

    /**
     * {@inheritdoc}
     */
    public function confirmForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
      $message = $this->t('<a href="#">Download PDF - ' . $form_state->getValue('your_name') . '</a>');
      $message = $this->replaceTokens($message, $this->getWebformSubmission());
      $this->messenger()->addStatus(Markup::create(Xss::filter($message)), FALSE);
      $this->debug(__FUNCTION__);
    }

    private function validateIPAddress(FormStateInterface $formState) {

        // retrun client remote address
        $ip = \Drupal::request()->getClientIp();
        $value = !empty($ip) ? Html::escape($ip) : NULL;

        //Check is IP adddress is set
        if (!empty($value)) {
            $formState->setValue('get_ip', $value);
        } else {
            // Empty ip address return text.
            $formState->setValue('get_ip', $this->t('IP address does not set.'));
        }
    }

  /**
   * Display the invoked plugin method to end user.
   *
   * @param string $method_name
   *   The invoked method name.
   * @param string $context1
   *   Additional parameter passed to the invoked method name.
   */
  protected function debug($method_name, $context1 = NULL) {
    if (!empty($this->configuration['debug'])) {
      $t_args = [
        '@id' => $this->getHandlerId(),
        '@class_name' => get_class($this),
        '@method_name' => $method_name,
        '@context1' => $context1,
      ];
      $this->messenger()->addWarning($this->t('Invoked @id: @class_name:@method_name @context1', $t_args), TRUE);
    }
  }
}
