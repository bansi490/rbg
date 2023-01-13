<?php

namespace Drupal\rbg_auto_lookup\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

class AutoLookupController extends ControllerBase {

    /**
     * Simple array is created and render on the webpage
     */
    public function page() {
        $items = [
            ['country' => 'UK'],
            ['country' => 'India'],
            ['country' => 'Canada'],
            ['country' => 'USA'],
        ];

        return [
            '#theme' => 'auto_lookup',
            '#title' => 'Custom autolook up item list',
            '#items' => $items,
        ];
    }

    /**
     * Render webform, which is inside the /src/Form directory
     */
    public function createAutoLookup(){
        $form = \Drupal::formBuilder()->getForm('Drupal\rbg_auto_lookup\Form\AutoLookupForm');
        $renderForm = \Drupal::service('renderer')->render($form);

        return [
            '#type'=>'markup',
            '#markup'=>$renderForm
        ];
    }

    /**
     * get the data from the database table and render in the table formate
     */
    public function getAddressList(){

        $query = \Drupal::database();
        $result = $query->select('customegovlpis','c')
                ->fields('c',['pao','streetName','postCode'])
                ->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10)
                ->execute()->fetchAll(\PDO::FETCH_OBJ);

        $adress = [];

        $params = \Drupal::request()->query->all();

        // print_r($params);
        // exit;

        foreach($result as $row){
            $adress[] = [
                'pao' => $row->pao,
                'streetName' => $row->streetName,
                'postcode' => $row->postCode
            ];
        }

        $header = ['Pao', 'StreetName', 'Postcode'];

        $build['table'] = [
            '#type'=>'table',
            '#header'=>$header,
            '#rows'=>$adress
        ];

        $build['parger'] = [
            '#type'=>'pager'
        ];

        return [
            $build,
            '#title'=>'Address list'
        ];
    }
}
