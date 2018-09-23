<?php

namespace Drupal\crediwireprojecttimemanager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\crediwireprojecttimemanager\Models\projectManagerModel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class projectsController.
 */
class projectsController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */


  public function renderprojectsTable(){
    $results =  $this->getProjectsList();
    $data = array();
    if ($results) {
      foreach ($results as $result) {
        if($result->project_state == 0 ){
          $Text = 'Start Time Log';
          $action = 'createlog';
        }
        else{
          $Text = 'End Time Log ';
          $action = 'closelogs';

        }
        $data[] = array(
          'project_name' => ['#markup' => $this->t($result->project_name)],
          'project_total_hours' => $result->project_total_hours,
          'project_state' =>  ['#markup' => $this->t('<a href="/admin/crediwire/project_manager/'.$action.'/'.$result->id.'" class="button js-form-submit form-submit">'.$Text.'</a>')],
          'logs' => ['#markup' => $this->t('<a href="/admin/crediwire/project_manager/projectLogs/'. $result->id.'" class="button js-form-submit form-submit">Se Logs</a>')]
        );
      }
    }
    return $data;
  }

  public function renderprojectslogsTable($id){
    $results =  $this->getLogListbyProjectsID($id);
    $data = array();
    if ($results) {
      foreach ($results as $result) {
        $data[] = array(
          'start_hour' => ['#markup' => $this->t($result->start_hour)],
          'end_hour' => $result->end_hour,
          'hours' => $result->end_hour
        );
      }
    }
    return $data;
  }

  public function getProjectsList() {
    $selection = array('project_name','project_total_hours','project_state','id');
    $condition = array(array('field'=>'active','data'=>1,'operator'=>'='));
    $managerModel = new projectManagerModel();
    $result = $managerModel->selectEntyti('crediwire_project_list',$condition,$selection);
    return $result;
  }

  public function getProjectbyid($id) {
    $selection = array('project_name','project_total_hours','project_state','id');
    $condition = array(array('field'=>'id','data'=>$id,'operator'=>'='));
    $managerModel = new projectManagerModel();
    $result = $managerModel->selectEntyti('crediwire_project_list',$condition,$selection);
    return $result;
  }

  public function getLogListbyProjectsID($projectsID) {
    $selection = array('date','start_hour','end_hour','hours');
    $condition = [
      array('field'=>'id_project','data'=>$projectsID,'operator'=>'='),
      array('field'=>'active','data'=>1,'operator'=>'=')
    ];
    $managerModel = new projectManagerModel();
    return  $managerModel->selectEntyti('crediwire_project_time_log',$condition,$selection);
  }

  public function insertProject($name) {
    $data = array(
      'project_name'=> $name,
      'project_total_hours'=> 0,
      'project_state'=> 0,
      'active'=> 1,
    );
    $managerModel = new projectManagerModel();
    $managerModel->insertEntyti('crediwire_project_list',$data);
    return true;
  }

  public function createlog(Request $request){
    $projectID = $request->attributes->get('id');
    $managerModel = new projectManagerModel();
    $date = date('Y-m-d H:i');
    $data = array(
      'date'=> $date ,
      'start_hour'=> $date ,
      'active'=> 1,
      'id_project'=> $projectID,
      'end_hour'=>0,
      'hours'=>0
    );
    $managerModel->insertEntyti('crediwire_project_time_log',$data);
    $this->updateState($projectID,1);
    $response = new RedirectResponse('/admin/crediwire/project_manager');
    return $response;
  }

  public function closelogs(Request $request){
    $projectID = $request->attributes->get('id');
    $managerModel = new projectManagerModel();
    $data = array(
      'end_hour'=> date('Y-m-d H:i'),
      'hours'=> 0,
    );
   $condition =array(
     array('field'=>'end_hour','data'=>0,'operator'=>'='),
     array('field'=>'id_project','data'=>$projectID,'operator'=>'=')
   );
    $managerModel->updateEntyti('crediwire_project_time_log',$condition,$data);
    $this->updateState($projectID,0);
    $response = new RedirectResponse('/admin/crediwire/project_manager');
    return $response;
  }

  public function updateState($id,$state){
    $fields = array('project_state'=> $state);
    $condition= array(array('field'=>'id','data'=>$id,'operator'=>'='));
    $managerModel = new projectManagerModel();
    $result = $managerModel->updateEntyti('crediwire_project_list',$condition,$fields);
    return true;
  }
}
