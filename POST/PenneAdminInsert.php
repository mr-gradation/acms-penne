<?php
namespace Acms\Plugins\Penne\POST;

use ACMS_POST; 
use DB;
use SQL;

class PenneAdminInsert extends ACMS_POST
{
  function post()
  {
    try{
      $Field = $this->extract('field');
      // var_dump($Field);
      // exit();

			$DB = DB::singleton(dsn());

      // group_id
      $group_id = $DB->query(SQL::nextval('penne_group_id', dsn(), true), 'seq');

      // グループのソート
      $SQL    = SQL::newSelect('penne_group');
      $SQL->setSelect('penne_group_sort');
      $SQL->addWhereOpr('penne_group_blog_id', BID);
      $SQL->setOrder('penne_group_sort', 'DESC');
      $group_sort = intval($DB->query($SQL->get(dsn()), 'one')) + 1;

      // グループのインサート 
      $SQL    = SQL::newInsert('penne_group');
      $SQL->addInsert('penne_group_id', $group_id);
      $SQL->addInsert('penne_group_sort', $group_sort);
      $SQL->addInsert('penne_group_title', $Field->get('penne_group_title'));
      $SQL->addInsert('penne_group_description', $Field->get('penne_group_description'));
      $SQL->addInsert('penne_group_status', $Field->get('penne_group_status'));
      $SQL->addInsert('penne_group_location', $Field->get('penne_group_location'));
      $SQL->addInsert('penne_group_condition', $Field->get('penne_group_condition'));
      $SQL->addInsert('penne_group_blog_id', BID);
      $DB->query($SQL->get(dsn()), 'exec');

      // フィールドのインサート
      $fields = [];
      foreach ($Field->getArray('@penne_field') as $key => $value) {
        foreach ($Field->getArray($value) as $key2 => $value2) {
          $fields[$key2][$value] = $value2;
        }
      }
      // var_dump($fields);
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
      $this->Post->set('id', $group_id);
      $this->Post->set('edit', 'update');
    } catch (\Exception $e) {
      $this->addError($e->getMessage());
    }
    return $this->Post; 
  }
}