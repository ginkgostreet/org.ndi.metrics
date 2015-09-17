<?php

require_once 'metrics.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function metrics_civicrm_config(&$config) {
  _metrics_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function metrics_civicrm_xmlMenu(&$files) {
  _metrics_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function metrics_civicrm_install() {
  _metrics_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function metrics_civicrm_uninstall() {
  _metrics_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function metrics_civicrm_enable() {
  _metrics_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function metrics_civicrm_disable() {
  _metrics_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function metrics_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _metrics_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function metrics_civicrm_managed(&$entities) {
  _metrics_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function metrics_civicrm_caseTypes(&$caseTypes) {
  _metrics_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function metrics_civicrm_angularModules(&$angularModules) {
_metrics_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function metrics_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _metrics_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function metrics_civicrm_preProcess($formName, &$form) {

}

*/

function metrics_metrics_collate(&$data) {

  /********[ Relationships ] ********/
  $sql = "SELECT COUNT(*) FROM civicrm_relationship";
  $total =& CRM_Core_DAO::singleValueQuery($sql);
  $data[] = array("type" => "relationships", "data" => $total);

  /********[ Activities ] ********/
  $sql = "SELECT COUNT(*) FROM civicrm_activity";
  $total =& CRM_Core_DAO::singleValueQuery($sql);
  $data[] = array("type" => "activities", "data" => $total);


  /********[ Tags ] ********/
  $sql = "SELECT name,COUNT(*) as total FROM civicrm_entity_tag LEFT JOIN civicrm_tag on (civicrm_entity_tag.tag_id = civicrm_tag.id) WHERE civicrm_entity_tag.entity_table = 'civicrm_contact' GROUP BY tag_id";
  $dao =& CRM_Core_DAO::executeQuery($sql);
  $totals = array();
  while($dao->fetch()) {
    $totals[$dao->name] = $dao->total;
  }
  $data[] = array("type" => "tags", "data" => $totals);


  /********[ Groups ] ********/
  $params = array();
  $groups = CRM_Contact_BAO_Group::getGroupList($params);
  $totals = array();
  foreach($groups as $group) {
    $totals[$group['title']] = $group['count'];
  }

  $data[] = array("type" => "groups", "data" => $totals);


  /********[ Mail ] ********/
  $mail = array();

  $sql = "SELECT COUNT(*) FROM civicrm_mailing_recipients";
  $mail['total_messages'] =& CRM_Core_DAO::singleValueQuery($sql);

  $sql = "SELECT COUNT(*) FROM (SELECT contact_id FROM civicrm_mailing_recipients GROUP BY contact_id)a";
  $mail['unique_recipients'] =& CRM_Core_DAO::singleValueQuery($sql);

  $sql = "SELECT COUNT(*) FROM civicrm_mailing_event_trackable_url_open";
  $mail['click_throughs'] =& CRM_Core_DAO::singleValueQuery($sql);

  $sql = "SELECT COUNT(*) FROM civicrm_mailing_event_opened";
  $mail['opens'] =& CRM_Core_DAO::singleValueQuery($sql);

  $sql = "SELECT COUNT(*) FROM civicrm_mailing_event_bounce";
  $mail['bounces'] =& CRM_Core_DAO::singleValueQuery($sql);

  $data[] = array("type" => "mailings", "data" => $mail);

  /********[ Registered Visits ] ********/


  /********[ Events ] ********/
  $totals = array();
  $sql = "SELECT COUNT(*) FROM civicrm_participant";
  $totals["total_participants"] =& CRM_Core_DAO::singleValueQuery($sql);

  $sql = "SELECT COUNT(*) FROM (SELECT contact_id FROM civicrm_participant GROUP BY contact_id)a";
  $totals["unique_participants"] =& CRM_Core_DAO::singleValueQuery($sql);

  $sql = "SELECT COUNT(*) FROM civicrm_event WHERE is_template = 0";
  $totals["total"] =& CRM_Core_DAO::singleValueQuery($sql);

  $data[] = array("type" => "events", "data" => $totals);

  /********[ Cases ] ********/


  /********[ Languages ] ********/


}
