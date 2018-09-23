<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/23/18
 * Time: 11:29 AM
 */
namespace Drupal\crediwireprojecttimemanager\Models;
use Drupal\Core\Controller\ControllerBase;
class projectManagerModel  {



  public function selectEntyti($table,$conditions,$selection){
    $database = \Drupal::database();
    $result = $database->select($table, 'x');
    $result->fields('x', $selection);
    foreach ($conditions as $condition){
      $result->condition('x.'.$condition["field"], $condition["data"], $condition["operator"]);
    }
    $exit = $result->execute()->fetchAll();
    return $exit;
  }

  public function insertEntyti($table,$data){
    $database = \Drupal::database();
    $database->insert($table)->fields($data)->execute();
    return true;
  }

  public function updateEntyti($table,$conditions,$fields){
    $database = \Drupal::database();
    $result = $database->update($table);
    $result->fields($fields);
    foreach ($conditions as $condition){
      $result->condition($condition["field"], $condition["data"], $condition["operator"]);
      }
    $result ->execute();
    return true;
  }


}