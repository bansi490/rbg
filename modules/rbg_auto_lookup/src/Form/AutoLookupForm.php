<?php
    namespace Drupal\rbg_auto_lookup\Form;
    use Drupal\Core\Form\FormBase;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Core\Database\Database;
    use Drupal\Core\Ajax\AjaxResponse;
    use Drupal\Core\Ajax\HtmlCommand;
    use Drupal\Core\Ajax\ReplaceCommand;

    class AutoLookupForm extends FormBase
    {
        /**
         * {@inheritdoc}
         */
        public function getFormId()
        {
            return 'postcodeLookup';
        }

        /**
         * {@inheritdoc}
         */
        public function buildForm(array $form, FormStateInterface $form_state)
        {

            $form['postcode'] = [
                '#type'=>'textfield',
                '#title'=>t('Postcode'),
                '#default_value'=>''
            ];

            $form['lookup'] = [
                '#type'=>'button',
                '#value'=>'Lookup',
                '#ajax'=>[
                    'callback'=>'::setAddressContainer',
                    'wrapper' =>'result_dropdown'
                ],
            ];

            $form['address'] = [
                '#type'=>'select',
                '#title'=>'Address',
                '#options'=>[],
                '#validated' => 'true',
                '#prefix'=>'<div id="result_dropdown" class="hidden">',
                '#suffix'=>'</div>'

            ];

            // $form['addressDiv'] = [
            //     '#type'=>'markup',
            //     '#markup'=>'<div id="result_dropdown"></div>'
            // ];

            $form['save'] = [
                '#type'=>'submit',
                '#value'=>'Save',
                '#button_type'=>'primary'
            ];

            return $form;
        }

        /**
         * {@inheritdoc}
         */
        public function setAddressContainer(array $form, FormStateInterface $form_state) {


            $query = \Drupal::database();
            $result = $query->select('customegovlpis','c')
                    ->condition('postCode', $form_state->getValue('postcode'), '=')
                    ->fields('c',['uprn','pao','streetName','postCode'])
                    ->execute()->fetchAll(\PDO::FETCH_OBJ);

            $address = [];

            foreach($result as $row){
                $address[] = [
                    $row->uprn =>
                        $row->pao .' '.
                        $row->streetName .' '.
                        $row->postCode

                ];
            }

            $addresses = [];
            foreach($address as $key => $value){
                foreach($value as $k => $v){
                    $addresses[$k] = $v;
                }
            }

            $elem = [
                '#type'=>'select',
                '#title'=>'Address',
                '#name'=>'address',
                '#id'=>'edit-address',
                '#options'=>$addresses,
                '#validated' => 'true',
                '#prefix'=>'<div id="result_dropdown">',
                '#suffix'=>'</div>'
            ];

            // $form['address'] = [
            //     '#type'=>'select',
            //     '#title'=>'Address',
            //     '#options'=>$addresses
            // ];

            $form_state->setRebuild(TRUE);

            $response = new AjaxResponse();
            // $response->addCommand(
            //     new HtmlCommand('#result_dropdown',  $form['address'], NULL)
            // );
            $response->addCommand(new ReplaceCommand('#result_dropdown', $elem));
            return $response;

        }

        /**
         * {@inheritdoc}
         */
        public function submitForm(array &$form, FormStateInterface $form_state)
        {
            $postData = [ $form_state->getValues()];

            echo "<pre>";
            print_r($postData);
            echo "</pre>";
            exit;
        }
    }
