<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/23/18
 * Time: 11:29 AM
 */

namespace Drupal\crediwireprojecttimemanager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class projectsController.
 */
class projectsController extends ControllerBase {

  /*Render Functions*/

  public function renderprojectsTable() {
    $results = $this->getProjectsList();
    $data = [];
    if ($results) {
      foreach ($results as $result) {
        if ($result->project_state == 0) {
          $Text = 'Start Time Log';
          $action = 'createlog';
        }
        else {
          $Text = 'End Time Log ';
          $action = 'closelogs';
        }
        $data[] = [
          'project_name' => ['#markup' => $this->t($result->project_name)],
          'project_total_hours' => round($result->project_total_hours, 3) . ' hours',
          'project_state' => ['#markup' => $this->t('<a href="/admin/crediwire/project_manager/' . $action . '/' . $result->id . '" class="btn btn-' . $action . '">' . $Text . '</a>')],
          'logs' => ['#markup' => $this->t('<a href="/admin/crediwire/project_manager/projectLogs/' . $result->id . '" class="btn btn-link">Se Logs</a>')],
          'action_pre' => ['#markup' => $this->t('<a href="/admin/crediwire/project_manager/action/' . $result->id . '/0" class="btn btn-closelogs">Desable</a>')],
        ];
      }
    }
    return $data;
  }

  public function getProjectsList() {
    $selection = [
      'project_name',
      'project_total_hours',
      'project_state',
      'id',
      'active',
    ];
    $condition = [['field' => 'active', 'data' => 1, 'operator' => '=']];
    $managerModel = new projectManagerModel();
    $result = $managerModel->selectEntity('crediwire_project_list', $condition, $selection);
    if ($result) {
      return $result;
    }
    else {
      return FALSE;
    }

  }

  public function renderhistoryprojectsTable() {
    $results = $this->gethistoryProjectsList();
    $data = [];
    if ($results) {
      foreach ($results as $result) {
        if ($result->project_state == 0) {
          $Text = 'Start Time Log';
          $action = 'createlog';
        }
        else {
          $Text = 'End Time Log ';
          $action = 'closelogs';
        }
        $data[] = [
          'project_name' => ['#markup' => $this->t($result->project_name)],
          'project_total_hours' => round($result->project_total_hours, 3) . ' hours',
          'logs' => ['#markup' => $this->t('<a href="/admin/crediwire/project_manager/projectLogs/' . $result->id . '" class="btn btn-link">Se Logs</a>')],
          'action_pre' => ['#markup' => $this->t('<a href="/admin/crediwire/project_manager/action/' . $result->id . '/1" class="btn btn-createlog">Enable</a>')],
          'action_delete' => ['#markup' => $this->t('<a href="/admin/crediwire/project_manager/delete/' . $result->id . '" class="btn btn-delete">Delete</a>')],
        ];
      }
    }
    return $data;
  }

  /*Getting information from DB*/

  public function gethistoryProjectsList() {
    $selection = [
      'project_name',
      'project_total_hours',
      'project_state',
      'id',
      'active',
    ];
    $condition = [['field' => 'active', 'data' => 0, 'operator' => '=']];
    $managerModel = new projectManagerModel();
    $result = $managerModel->selectEntity('crediwire_project_list', $condition, $selection);
    if ($result) {
      return $result;
    }
    else {
      return FALSE;
    }

  }

  public function renderprojectslogsTable($id) {
    $results = $this->getLogListbyProjectsID($id);
    $data = [];
    if ($results) {
      foreach ($results as $result) {
        $data[] = [
          'start_hour' => ['#markup' => $this->t($result->start_hour)],
          'end_hour' => $result->end_hour,
          'hours' => $result->hours,
        ];
      }
    }
    return $data;
  }

  public function getLogListbyProjectsID($projectsID) {
    $selection = ['date', 'start_hour', 'end_hour', 'hours'];
    $condition = [
      ['field' => 'id_project', 'data' => $projectsID, 'operator' => '='],
      ['field' => 'active', 'data' => 1, 'operator' => '='],
    ];
    $managerModel = new projectManagerModel();
    $result = $managerModel->selectEntity('crediwire_project_time_log', $condition, $selection);
    if ($result) {
      return $result;
    }
    else {
      drupal_set_message('We found some issues, please contact your system administrator');
    }
  }

  public function getProjectbyid($id) {
    $selection = ['project_name', 'project_total_hours', 'project_state', 'id'];
    $condition = [['field' => 'id', 'data' => $id, 'operator' => '=']];
    $managerModel = new projectManagerModel();
    $result = $managerModel->selectEntity('crediwire_project_list', $condition, $selection);
    if ($result) {
      return $result;
    }
    else {
      drupal_set_message('We found some issues, please contact your system administrator');
    }
  }

  /*Insert and Update information in DB*/

  public function insertProject($name) {
    $managerModel = new projectManagerModel();
    $condition = [
      [
        'field' => 'project_name',
        'data' => $name,
        'operator' => '=',
      ],
    ];
    $fields = ['project_name'];
    $project = $managerModel->selectEntity('crediwire_project_list', $condition, $fields);
    if (!$project) {
      $data = [
        'project_name' => $name,
        'project_total_hours' => 0,
        'project_state' => 0,
        'active' => 1,
      ];
      $managerModel->insertEntity('crediwire_project_list', $data);
    }
    else {
      drupal_set_message('Project name "' . $name . '" it already exists');
    }
    return TRUE;
  }

  public function createlog(Request $request) {
    $projectID = $request->attributes->get('id');
    $managerModel = new projectManagerModel();
    $date = date('Y-m-d H:i:s');
    $data = [
      'date' => $date,
      'start_hour' => $date,
      'active' => 1,
      'id_project' => $projectID,
      'end_hour' => 0,
      'hours' => 0,
    ];
    $managerModel->insertEntity('crediwire_project_time_log', $data);
    $this->updateState($projectID, 1);
    $response = new RedirectResponse('/admin/crediwire/project_manager');
    if ($response) {
      return $response;
    }
    else {
      drupal_set_message('We found some issues, please contact your system administrator');
    }
  }

  public function updateState($id, $state) {
    $fields = ['project_state' => $state];
    $condition = [['field' => 'id', 'data' => $id, 'operator' => '=']];
    $managerModel = new projectManagerModel();
    $response = $managerModel->updateEntity('crediwire_project_list', $condition, $fields);
    if ($response) {
      return TRUE;
    }
    else {
      drupal_set_message('We found some issues, please contact your system administrator');
    }

  }

  public function closelogs(Request $request) {
    $projectID = $request->attributes->get('id');
    $managerModel = new projectManagerModel();
    $condition = [
      ['field' => 'end_hour', 'data' => 0, 'operator' => '='],
      ['field' => 'id_project', 'data' => $projectID, 'operator' => '='],
    ];
    $selection = ['start_hour'];
    $start_hour = $managerModel->selectEntity('crediwire_project_time_log', $condition, $selection);
    $end_date = date('Y-m-d H:i:s');
    $TotalHours = $this->totalhours($start_hour[0]->start_hour, $end_date);
    $TotalLogHours = $TotalHours['Days'] . " days - " .
      $TotalHours['Hours'] . " hours - " .
      $TotalHours['Minutes'] . " min - " .
      $TotalHours['Seg'] . " seg";
    $TotalLogHoursProject = ($TotalHours['Days'] * 24) + $TotalHours['Hours'] + ($TotalHours['Minutes'] / 60) + ($TotalHours['Seg'] / 3600);
    $this->updateProjectHours($TotalLogHoursProject, $projectID);
    $data = [
      'end_hour' => $end_date,
      'hours' => $TotalLogHours,
    ];
    $condition = [
      ['field' => 'end_hour', 'data' => 0, 'operator' => '='],
      ['field' => 'hours', 'data' => 0, 'operator' => '='],
      ['field' => 'id_project', 'data' => $projectID, 'operator' => '='],
    ];
    $managerModel->updateEntity('crediwire_project_time_log', $condition, $data);

    $this->updateState($projectID, 0);
    $response = new RedirectResponse('/admin/crediwire/project_manager');

    if ($response) {
      return $response;
    }
    else {
      drupal_set_message('We found some issues, please contact your system administrator');
    }


  }

  public function totalhours($start, $end) {
    $fecha1 = date_create_from_format("Y-m-d H:i:s", $start);
    $fecha2 = date_create_from_format("Y-m-d H:i:s", $end);
    $fecha = date_diff($fecha1, $fecha2);
    $diff = [
      'Days' => $fecha->d,
      'Hours' => $fecha->h,
      'Minutes' => $fecha->i,
      'Seg' => $fecha->s,
    ];
    if ($diff) {
      return $diff;
    }
    else {
      drupal_set_message('We found some issues, please contact your system administrator');
    }
  }

  public function updateProjectHours($TotalLogHoursProject, $projectID) {
    $managerModel = new projectManagerModel();
    $condition = [['field' => 'id', 'data' => $projectID, 'operator' => '=']];
    $selection = ['project_total_hours'];
    $start_hour = $managerModel->selectEntity('crediwire_project_list', $condition, $selection);
    $total = $TotalLogHoursProject + $start_hour[0]->project_total_hours;
    $data = ['project_total_hours' => $total];
    $response = $managerModel->updateEntity('crediwire_project_list', $condition, $data);
    if ($response) {
      return TRUE;
    }
    else {
      drupal_set_message('We found some issues, please contact your system administrator');
    }
  }

  public function updateProject(Request $request) {
    $state = $request->attributes->get('state');
    $id = $request->attributes->get('id');
    $fields = ['active' => $state];
    $condition = [['field' => 'id', 'data' => $id, 'operator' => '=']];
    $managerModel = new projectManagerModel();
    $response = $managerModel->updateEntity('crediwire_project_list', $condition, $fields);
    if ($response) {
      $url = new RedirectResponse('/admin/crediwire/project_manager');
      return $url;
    }
    else {
      drupal_set_message('We found some issues, please contact your system administrator');
    }

  }

  public function deleteProject(Request $request) {
    $id = $request->attributes->get('id');
    $condition = [['field' => 'id', 'data' => $id, 'operator' => '=']];
    $condition_ = [['field' => 'id_project', 'data' => $id, 'operator' => '=']];
    $managerModel = new projectManagerModel();
    $managerModel->deleteEntity('crediwire_project_list', $condition);
    $managerModel->deleteEntity('crediwire_project_time_log', $condition_);
    $url = new RedirectResponse('/admin/crediwire/project_manager');
    return $url;

  }
}
