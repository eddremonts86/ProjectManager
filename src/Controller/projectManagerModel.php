<?php
/**
 * Created by PhpStorm.
 * User: edd
 * Date: 9/23/18
 * Time: 11:29 AM
 */

namespace Drupal\crediwireprojecttimemanager\Controller;

class projectManagerModel {

  /*
   * Class to make all the interactions between  System and DataBase
   *
   *Parameters
   * $tableName - Table name to make the query
   *
   *   $conditions - Expecting an array with all the paramameters for the query.
   *     Array elements
   *       - "field" : Database fields
   *       - "data" : Data content
   *       - "operation" : operation (= ,<=, >,...)
   *
   *   $selection - Array with table keys
   *
   *   $fields - Array with table keys and value to update
   *
   *   $data - Array with table keys and value to update
   *
   * */

  public function selectEntity($table, $conditions, $selection) {
    $database = \Drupal::database();
    $result = $database->select($table, 'x');
    $result->fields('x', $selection);
    foreach ($conditions as $condition) {
      $result->condition('x.' . $condition["field"], $condition["data"], $condition["operator"]);
    }
    $exit = $result->execute()->fetchAll();
    return $exit;
  }

  public function insertEntity($table, $data) {
    $database = \Drupal::database();
    $database->insert($table)->fields($data)->execute();
    return TRUE;
  }

  public function updateEntity($table, $conditions, $fields) {
    $database = \Drupal::database();
    $result = $database->update($table);
    $result->fields($fields);
    foreach ($conditions as $condition) {
      $result->condition($condition["field"], $condition["data"], $condition["operator"]);
    }
    $result->execute();
    return TRUE;
  }

  public function deleteEntity($table, $conditions) {
    $database = \Drupal::database();
    $result = $database->delete($table);
    foreach ($conditions as $condition) {
      $result->condition($condition["field"], $condition["data"], $condition["operator"]);
    }
    $result->execute();
    return TRUE;
  }
}