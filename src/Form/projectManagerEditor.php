<?php

namespace Drupal\crediwireprojecttimemanager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\crediwireprojecttimemanager\Controller\projectsController;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class projectManagerEditor.
 */
class projectManagerEditor extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'project_manager_editor';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$id = null) {
    $project = new  projectsController();
    $projectID = $id;
    $projectName = $project->getProjectbyid($projectID);
    $form['table'] = [
      '#type' => 'fieldset',
      '#title' => $projectName[0]->project_name,
    ];
    $form['table']['table'] = [
    '#type' => 'tableselect',
    '#header' => [
      'start_hour' => t('Start'),
      'end_hour' => t('End'),
      'hours' => t('Total')
    ],
    '#options' => $project->renderprojectslogsTable($projectID),
    '#open' => TRUE,
    '#empty' => t('No projects found'),
    '#attributes' => ['id' => 'tableid']
  ];
    $form['submit'] = [
      '#type' => 'imput',
      '#markup' => $this->t('<a href="/admin/crediwire/project_manager">Go back</a>')
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
