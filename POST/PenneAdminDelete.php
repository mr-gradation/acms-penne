<?php
namespace Acms\Plugins\Penne\POST;

use ACMS_POST; 
use DB;
use SQL;
use ACMS_Validator;

class PenneAdminDelete extends ACMS_POST
{
  function post()
  {
    try{
      $Field = $this->extract('field');
      $Field->validate(new ACMS_Validator());
      if ( $this->Post->isValidAll() ) {
        $DB = DB::singleton(dsn());

        // group_id
        $group_id = intval($this->Post->get('id'));

        // グループの削除 
        $SQL = SQL::newDelete('penne_group');
        $SQL->addWhereOpr('penne_group_id', $group_id);
        $SQL->addWhereOpr('penne_group_blog_id', BID);
        $DB->query($SQL->get(dsn()), 'exec');

        // フィールドの削除
        $SQL = SQL::newDelete('penne_field');
        $SQL->addWhereOpr('penne_field_group_id', $group_id);
        $DB->query($SQL->get(dsn()), 'exec');

        $this->addMessage("削除しました。");
        $this->Post->set('edit', 'delete');
      }
    } catch (\Exception $e) {
      $this->addError($e->getMessage());
    }
    return $this->Post; 
  }
}