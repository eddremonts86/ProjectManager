<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/23/18
 * Time: 11:29 AM
 */
namespace Drupal\crediwireprojecttimemanager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\crediwireprojecttimemanager\Controller\projectsController;

/**
 * Class projectManager.
 */
class projectManager extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'project_manager';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $project = new  projectsController();
    $form['information'] = array(
      '#type' => 'vertical_tabs',
      '#default_tab' => 'table',
    );

    $form['table'] = array(
      '#type' => 'details',
      '#title' => $this->t('Projects List'),
      '#group' => 'information',
    );
    $form['table']['table'] = [
      '#type' => 'tableselect',
      '#header' => [
        'project_name' => t('Project Name'),
        'project_total_hours' => t('Total Hours'),
        'project_state' => t('State'),
        'logs' =>t('Se Logs'),
        'action_pre' => t('Disable Projects')
      ],
      '#options' => $project->renderprojectsTable(),
      '#open' => TRUE,
      '#empty' => t('No projects found'),
      '#attributes' => ['id' => 'tableid']
    ];

    $form['history'] = array(
      '#type' => 'details',
      '#title' => $this->t('History Projects List'),
      '#group' => 'information',
    );
    $form['history']['history_table'] = [
      '#type' => 'tableselect',
      '#header' => [
        'project_name' => t('Project Name'),
        'project_total_hours' => t('Total Hours'),
        'project_state' => t('State'),
        'logs' =>t('Se Logs'),
        'action_pre' => t('Enable Projects')
      ],
      '#options' => $project->renderhistoryprojectsTable(),
      '#open' => TRUE,
      '#empty' => t('No projects found'),
      '#attributes' => ['id' => 'tableid']
    ];

    $form['project'] = array(
      '#type' => 'details',
      '#title' => $this->t('Create Projects'),
      '#group' => 'information',
    );
    $form['project']['form'] = [
      '#type' => 'fieldset',
      '#title' => $this
        ->t('Add New Project'),
    ];
    $form['project']['form']['project_name'] = [
      '#type' => 'textfield',
      '#title' => 'Projects Name',
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => '',
      '#description' => '',
    ];
    $form['project']['form']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
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
    $onlyUse =array('project_name');
    echo "<pre>";
    foreach ($form_state->getValues() as $key => $value) {
      if(in_array($key,$onlyUse) and $value != ''){
        $project = new  projectsController();
        $project->insertProject($value);
      }
    }
  }
}
