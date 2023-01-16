<?php
    namespace Drupal\rbg_schoolyearcheck\Form;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Core\Ajax\AjaxResponse;
    use Drupal\Core\Ajax\HTMLCommand;
    use Drupal\Core\Datetime;

    class rbgschoolyearcheckForm extends FormBase
    {
        /**
         * {@inheritdoc}
         */
        public function getFormId()
        {
            return 'schoolyearcheckForm';
        }

        /**
         * {@inheritdoc}
         */
        public function buildForm(array $form, FormStateInterface $form_state)
        {

            $form['dob'] = [
                '#type'=>'date',
                '#title'=>$this->t('Date of birth'),
                '#default_value'=>''
            ];

            $form['lookup'] = [
                '#type'=>'button',
                '#value'=>$this->t('Check'),
                '#ajax'=>[
                    'callback'=>'::setMessage'
                ],
            ];

            $form['message'] = [
                '#type'=>'markup',
                '#markup'=>'<div class="result_message"></div>'
            ];

            //$form['#attached']['library'][] = 'ajaxExample/log';

            return $form;
        }

        /**
         * {@inheritdoc}
         */
        public function setMessage(array &$form, FormStateInterface $form_state) {

            $response = new AjaxResponse();

            $response->addCommand(
                new HTMLCommand('.result_message', $this->getMessage($form_state->getValue('dob')))
            );

            return $response;
        }

        /**
         * {@inheritdoc}
         */
        public function getMessage($dob) {

          // fetch the birth month and year from the Date of birth.
          $thisMonth = Datetime\DrupalDateTime::createFromFormat('Y-m-d', $dob)->format('m');
          $thisYear = Datetime\DrupalDateTime::createFromFormat('Y-m-d', $dob)->format('Y');

          if($thisMonth < 9){
            $currentAcadYearStart = $thisYear - 1;
          } else {
            $currentAcadYearStart = $thisYear;
          }

          $currentAcadYearEnd = $currentAcadYearStart + 1;

          //calculate year group
          if (date('m', strtotime($dob)) < 9) {
            $ageForAdmission = $currentAcadYearStart - $thisYear;
          } else {
            $ageForAdmission = $currentAcadYearStart - $thisYear - 1;
          }

          //To tidy this
          //We have current academic year
          /*
          Need to handle:
          1. Negative ageForAdmission ie born after current academic year
          2. When the child starts primary school
          3. When the child starts secondary school
          4. Over age

          $schoolYear 0 for reception in current acad year
          if $schoolYear less than -2, children's centres
          if $schoolYear is -2 and current month before August, children's centres
          if $schoolYear is -2 and current month is August, read on
          if $schoolYear is -1 read on
          if $schoolYear is 0 to 5, in-year
          if $schoolYear is 6, in-year
          if $schoolYear is 6 and current month is August - secondary
          if $schoolYear is 7 to 11, in-year
          if $schoolYear is more than 11, post-16 and adult


          info to display:

          dob
          error text
          year to start primary school - if in the future
          additional text

          */

          $schoolYear = $ageForAdmission - 4;
          $priAcadYear =  'September ' . ($currentAcadYearStart - $schoolYear);

          if ($schoolYear < -2) {
            $textAction = "The child will start primary school in <strong>" . $priAcadYear . "</strong>. You're not yet able to apply for a primary school place. You may wish to find out about our children's centres.";
            $linkText = "Find a children's centre";
            $linkURL = "http://www.royalgreenwich.gov.uk/childrenscentres";
          }
          if($schoolYear == -2) {
            if ($thisMonth == 8) {
              $textAction = "The child will start primary school in <strong>" . $priAcadYear . "</strong>. You should apply for a primary school place. <strong>Please read on</strong>.";
            } else {
              $textAction = "The child will start primary school in <strong>" . $priAcadYear . "</strong>. You're not yet able to apply for a primary school place. You may wish to find out about our children's centres.";
              $linkText = "Find a children's centre";
            }
          }

          if ($schoolYear == -1) {
            $textAction = "The child will start primary school in <strong>" . $priAcadYear . "</strong>. You should apply for a primary school place if you haven't done so. You should apply even if you've missed the deadline. <strong>Please read on</strong>.";
          }

          if ($schoolYear >= 0 && $schoolYear <= 5) {
            $textAction = "The child should already be in a primary school. If you're changing school during the academic year, please apply for in-year admission.";
            $linkText = "Apply for in-year admission";
            $linkURL = "http://www.royalgreenwich.gov.uk/inyearadmissions";
          }

          if ($schoolYear == 6) {
            if ($thisMonth == 8) {
              $textAction = "The child should be finishing primary school, and will need to apply for secondary admission.";
              $linkText = "Find out about secondary school admission";
              $linkURL = "http://www.royalgreenwich.gov.uk/secondaryadmissions";
            } else {
              $textAction = "The child should already be in a primary school. If you're changing school during the academic year, please apply for in-year admission. You'll also need to apply for secondary admission later in the year.";
              $linkText = "Apply for in-year admission";
              $linkURL = "http://www.royalgreenwich.gov.uk/inyearadmissions";
            }
          }
          if ($schoolYear >= 7 && $schoolYear <= 11) {
            $textAction = "The child should be in secondary school. If you're changing school during the academic year, please apply for in-year admission.";
            $linkText = "Apply for in-year admission";
            $linkURL = "http://www.royalgreenwich.gov.uk/inyearadmissions";
          }

          if ($schoolYear > 11) {
            $textAction = "The child is too old to be in primary or secondary school. Find out about Post-16 or adult learning options.";
            $linkText = "Find Post-16 opportunities";
            $linkURL = "http://www.royalgreenwich.gov.uk/post16";
          }

          if($linkURL != '') {
            $linkHTML = "<p><a class='buttonlink' target='_top' href='" . $linkURL . "''>" . $linkText . "</a></p>";
          } else {
            $linkHTML = '';
          }
          //<p><strong>Current academic year</strong>: " . $currentAcadYearStart . "-" . $currentAcadYearEnd . "</p>
          $textSuccess="
            <p><strong>Child's date of birth</strong>: " . str_replace('-','/',$dob) . "</p>
            <p>" . $textAction . "</p>" . $linkHTML;
          $textError = "";

          if(strtotime($dob) > time()) {
            $textError = "<p>You've entered a date of birth that's after today's date. Please try again.</p>";
            $textSuccess = "";
          }
          return $textAction;
        }

        /**
         * {@inheritdoc}
         */
        public function submitForm(array &$form, FormStateInterface $form_state)
        {

        }
    }
