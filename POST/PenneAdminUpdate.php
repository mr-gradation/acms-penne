<?php
namespace Acms\Plugins\Penne\POST;

use ACMS_POST; 
use DB;
use SQL;
use ACMS_Validator;

class PenneAdminUpdate extends ACMS_POST
{
  function post()
  {
    try{
      $Field = $this->extract('field');
      $Field->validate(new ACMS_Validator());
      if ( $this->Post->isValidAll() ) {
        // group_id
        $group_id = intval($this->Post->get('id'));

        // グループのアップデート 
        $DB = DB::singleton(dsn());
        $SQL = SQL::newUpdate('penne_group');
        $SQL->addUpdate('penne_group_title', $Field->get('penne_group_title'));
        $SQL->addUpdate('penne_group_description', $Field->get('penne_group_description'));
        $SQL->addUpdate('penne_group_status', $Field->get('penne_group_status'));
        $SQL->addUpdate('penne_group_location', $Field->get('penne_group_location'));
        $SQL->addUpdate('penne_group_condition', $Field->get('penne_group_condition'));
        $SQL->addWhereOpr('penne_group_id', $group_id);
        $SQL->addWhereOpr('penne_group_blog_id', BID);
        $DB->query($SQL->get(dsn()), 'exec');

        // フィールドのアップデート
        $SQL = SQL::newDelete('penne_field');
        $SQL->addWhereOpr('penne_field_group_id', $group_id);
        $DB->query($SQL->get(dsn()), 'exec');
        // var_dump($SQL->get(dsn()));

        $fields = [];
        foreach ($Field->getArray('@penne_field') as $key => $value) {
          foreach ($Field->getArray($value) as $key2 => $value2) {
            $fields[$key2][$value] = $value2;
          }
        }
        foreach ($fields as $key => $value) {
          $field_id = $DB->query(SQL::nextval('penne_field_id', dsn(), true), 'seq');

          $SQL = SQL::newInsert('penne_field');
          $SQL->addInsert('penne_field_id', $field_id);
          $SQL->addInsert('penne_field_sort', $key);
          $SQL->addInsert('penne_field_group_id', $group_id);
          foreach ($value as $key2 => $value2) {
            $SQL->addInsert($key2, $value2);
          }
          // var_dump($SQL->get(dsn()));
          $DB->query($SQL->get(dsn()), 'exec');
        }
        
        $this->addMessage("保存しました。");
        $this->Post->set('edit', 'update');
      }
    } catch (\Exception $e) {
      $this->addError($e->getMessage());
    }
    return $this->Post; 
  }
}